<?php

namespace App\trait;

use Illuminate\Support\Facades\Storage;

trait image
{


    public function storeBase64Image($base64Image, $folderPath = 'admin/unit/images') {

        // Validate if the base64 string has a valid image MIME type
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            // Extract the image MIME type
            $imageType = $type[1]; // e.g., 'jpeg', 'png', 'gif', etc.

            // Extract the actual base64 encoded data (remove the data URL part)
            $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
            $imageData = base64_decode($imageData);

            // Generate a unique file name with the appropriate extension
            $fileName = uniqid() . '.' . $imageType;

            // Save the image to the storage disk (default is local)
            Storage::disk('public')->put($folderPath . '/' . $fileName, $imageData);

            // Return the image path
            return $folderPath . '/' . $fileName;
        }

        return null;
    }

    public function storeBase64File($base64File, $folderPath = 'uploads') {
        if (preg_match('/^data:(\w+)\/(\w+);base64,/', $base64File, $matches)) {
            $extension = $matches[2];
            $fileData = substr($base64File, strpos($base64File, ',') + 1);
            $fileData = base64_decode($fileData);
            $fileName = uniqid() . '.' . $extension;
            Storage::disk('public')->put($folderPath . '/' . $fileName, $fileData);
            return $folderPath . '/' . $fileName;
        }
        return null;
    }


    public function deleteImage($imagePath){
        // Check if the file exists
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
