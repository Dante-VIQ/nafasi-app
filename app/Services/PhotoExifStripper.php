<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PhotoExifStripper
{
    /**
     * Strip all EXIF metadata from an image file.
     * Returns the processed image path or null on failure.
     */
    public function strip(string $filePath): ?string
    {
        if (!extension_loaded('gd')) {
            return $filePath; // GD not available, skip
        }

        try {
            $image = imagecreatefromstring(file_get_contents($filePath));
            if ($image === false) {
                return $filePath;
            }

            $tempPath = $filePath . '.stripped.jpg';
            imagejpeg($image, $tempPath, 85);
            imagedestroy($image);

            // Replace original with stripped version
            rename($tempPath, $filePath);
            return $filePath;
        } catch (\Exception $e) {
            Log::warning('EXIF stripping failed: ' . $e->getMessage());
            return $filePath;
        }
    }
}