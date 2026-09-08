<?php
// convert-to-webp.php - Run this script once to convert images to WebP
// Access: https://thepublicexpress.com/convert-to-webp.php

$directory = storage_path('app/public/news');
$images = [];

function convertToWebP($source, $quality = 80) {
    $info = getimagesize($source);
    if (!$info) return false;
    
    $mime = $info['mime'];
    $output = pathinfo($source, PATHINFO_DIRNAME) . '/' . pathinfo($source, PATHINFO_FILENAME) . '.webp';
    
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;
        case 'image/webp':
            return $source; // Already WebP
        default:
            return false;
    }
    
    if (!$image) return false;
    
    // Resize if too large (max 1200px)
    $width = imagesx($image);
    $height = imagesy($image);
    if ($width > 1200) {
        $newWidth = 1200;
        $newHeight = ($height * 1200) / $width;
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $resized;
    }
    
    imagewebp($image, $output, $quality);
    imagedestroy($image);
    
    // Check if WebP is smaller
    if (file_exists($output)) {
        $originalSize = filesize($source);
        $webpSize = filesize($output);
        if ($webpSize < $originalSize) {
            unlink($source);
            return $output;
        } else {
            unlink($output);
            return $source;
        }
    }
    return $source;
}

echo "<h2>🔄 Converting Images to WebP...</h2>";

// Recursively find all images
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
);

$count = 0;
foreach ($iterator as $file) {
    if ($file->isFile()) {
        $ext = strtolower($file->getExtension());
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $result = convertToWebP($file->getPathname());
            if ($result) {
                $count++;
                echo "✅ Converted: " . $file->getFilename() . "<br>";
            }
        }
    }
}

echo "<h3>✅ Total Converted: $count images</h3>";
echo "<p><a href='/'>Go Home</a></p>";