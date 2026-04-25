<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class ImageHelper
{
    public static function uploadAnnouncementImage(UploadedFile $image): string
    {
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $directory = public_path('img-announcements');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $image->move($directory, $filename);

        return 'img-announcements/' . $filename;
    }
    
    public static function deleteAnnouncementImage(string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }

        $fullPath = public_path($imagePath);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
    
    public static function getImageUrl(string $imagePath): string
    {
        if (!$imagePath) {
            return asset('images/default-announcement.jpg');
        }

        if (preg_match('/^https?:\/\//', $imagePath)) {
            return $imagePath;
        }

        return url($imagePath);
    }
    
    public static function validateImage(UploadedFile $image): array
    {
        $allowedTypes = ['jpeg', 'jpg', 'png', 'gif'];
        $maxSize = 2048; // KB
        
        $errors = [];
        
        if (!in_array(strtolower($image->getClientOriginalExtension()), $allowedTypes)) {
            $errors[] = 'Invalid image type. Allowed types: ' . implode(', ', $allowedTypes);
        }
        
        if ($image->getSize() > $maxSize * 1024) {
            $errors[] = 'Image size must be less than ' . $maxSize . 'KB';
        }
        
        return $errors;
    }
}
