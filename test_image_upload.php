use Illuminate\Http\UploadedFile;
use App\Services\FileService;
use Illuminate\Support\Facades\Storage;

// Mock an uploaded file
$image = UploadedFile::fake()->image('large-test-image.jpg', 3000, 2000);

echo "Original Mime: " . $image->getMimeType() . "\n";
echo "Original Size: " . $image->getSize() . "\n";

$service = app(FileService::class);
$url = $service->upload($image, 'test_uploads');

echo "Uploaded URL: " . $url . "\n";

// Check the file
$path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));
$fullPath = storage_path('app/public/' . $path);

echo "Checking file at: " . $fullPath . "\n";

if (file_exists($fullPath)) {
    echo "File exists!\n";
    $info = getimagesize($fullPath);
    echo "Dimensions: " . $info[0] . "x" . $info[1] . "\n";
    echo "Mime: " . $info['mime'] . "\n";
    
    if ($info[0] <= 1200 && $info['mime'] === 'image/webp') {
        echo "SUCCESS: Image resized and converted to WebP.\n";
    } else {
        echo "FAILURE: Image not processed correctly.\n";
    }
} else {
    echo "File not found.\n";
}
