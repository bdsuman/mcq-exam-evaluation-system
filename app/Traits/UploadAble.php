<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Interfaces\EncoderInterface;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;

trait UploadAble
{
    /**
     * Upload and optionally process image files. Other files are stored as-is.
     *
     * @param UploadedFile $file
     * @param string|null $folder
     * @param string $disk
     * @param string|null $filename
     * @param array $options ['crop' => [...], 'resize' => [...], 'quality' => int]
     * @return string|false
     */
    public function uploadFile(
        UploadedFile $file,
        ?string $folder = null,
        string $disk = 'public',
        ?string $filename = null,
        array $options = []
    ): string|false {
        $name = $filename ?? uniqid('FILE_') . '_' . dechex(time());
        $extension = strtolower($file->getClientOriginalExtension());
        $fullName = $name . '.' . $extension;
        $path = $folder ? $folder . '/' . $fullName : $fullName;

        $imageTypes = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($extension, $imageTypes)) {
            try {
              $manager = new ImageManager(new GdDriver());

            // Load image from file
            $image = $manager->read($file->getRealPath());
            $image = $image->scaleDown(width: 1920, height: 1080);
                // Crop if requested
                if (!empty($options['crop'])) {
                    $crop = $options['crop'];
                    $image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y']);
                }

                // Resize if requested
                if (!empty($options['resize'])) {
                    $resize = $options['resize'];
                    $image->resize($resize['width'], $resize['height'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Set quality (default 90)
                $quality = $options['quality'] ?? 90;

                // Save processed image
                Storage::disk($disk)->put($path, (string) $image->encode($this->getEncoder($extension, $quality)));
                return $path;
            } catch (\Exception $e) {
                // Fallback to raw upload if processing fails
                return $file->storeAs($folder, $fullName, $disk);
            }
        }

        // Non-image file: upload as-is
        return $file->storeAs($folder, $fullName, $disk);
    }

    public function deleteFile(?string $path = null, string $disk = 'public'): void
    {
        if ($path) {
            Storage::disk($disk)->delete($path);
        }
    }

    protected function getEncoder(string $format, int $quality = 80): EncoderInterface
    {
        return match ($format) {
            'jpg', 'jpeg' => new JpegEncoder(quality: $quality),
            'png'         => new PngEncoder(9),
            'webp'        => new WebpEncoder(quality: $quality),
            default       => throw new \InvalidArgumentException("Unsupported format: $format"),
        };
    }

}
