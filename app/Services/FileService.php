<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Upload a file to the specified directory.
     *
     * @return string Full URL of the uploaded file
     */
    public function upload(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $disk = env('FILESYSTEM_DISK', $disk);
        
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
