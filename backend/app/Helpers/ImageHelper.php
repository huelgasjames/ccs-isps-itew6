<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageHelper
{
    public static function uploadAnnouncementImage(UploadedFile $image): string
    {
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        
        // Store in public disk
        $path = $image->storeAs('announcements', $filename, 'public');
        
        return $path;
    }
    
    public static function deleteAnnouncementImage(string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }
        
        return Storage::disk('public')->delete($imagePath);
    }
    
    public static function getImageUrl(string $imagePath): string
    {
        if (!$imagePath) {
            return asset('images/default-announcement.jpg');
        }
        
        return Storage::url($imagePath);
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
