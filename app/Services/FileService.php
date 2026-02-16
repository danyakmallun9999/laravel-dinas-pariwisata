<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Allowed file extensions (lowercase).
     * HIGH-04: Prevents upload of executable files (PHP, shell scripts, etc.).
     */
    private array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv',
    ];

    /**
     * Dangerous patterns in filenames (e.g. shell.php.jpg).
     */
    private array $dangerousPatterns = [
        '/\.(php|phtml|phar|sh|bash|exe|bat|cmd|com|ps1|py|rb|pl|cgi|asp|aspx|jsp|war)\./i',
    ];

    /**
     * Upload a file to the specified directory.
     *
     * @return string Full URL of the uploaded file
     */
    public function upload(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $disk = env('FILESYSTEM_DISK', $disk);

        // HIGH-04: Validate extension whitelist
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new \InvalidArgumentException("File type not allowed: {$extension}");
        }

        // HIGH-04: Block double extensions (e.g., shell.php.jpg)
        $originalName = $file->getClientOriginalName();
        foreach ($this->dangerousPatterns as $pattern) {
            if (preg_match($pattern, $originalName)) {
                throw new \InvalidArgumentException("Suspicious file name detected.");
            }
        }

        // Check if file is an image
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file);
            
            // Resize if width > 1200
            if ($image->width() > 1200) {
                $image->scale(width: 1200);
            }
            
            // Encode to WebP for optimization
            $encoded = $image->toWebp(quality: 80);
            
            // Generate unique filename with webp extension
            $filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
            $path = $directory . '/' . $filename;
            
            Storage::disk($disk)->put($path, (string) $encoded);
        } else {
            // Non-image files, just store normally
            $path = $file->store($directory, $disk);
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Delete a file from storage.
     *
     * @param  string|null  $path  Full URL or path
     */
    public function delete(?string $path, string $disk = 'public'): void
    {
        if (! $path) {
            return;
        }

        $disk = env('FILESYSTEM_DISK', $disk);

        // Extract relative path
        $baseUrl = Storage::disk($disk)->url('');
        $relativePath = str_replace($baseUrl, '', $path);

        // Fallback cleanup if URL extraction fails (e.g. local vs s3)
        if ($relativePath === $path && $disk === 'public') {
            $relativePath = str_replace('/storage/', '', $path);
            $relativePath = str_replace('storage/', '', $relativePath);
        }

        if (Storage::disk($disk)->exists($relativePath)) {
            Storage::disk($disk)->delete($relativePath);
        }
    }
}
