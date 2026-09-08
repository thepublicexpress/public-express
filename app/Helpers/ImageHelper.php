<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    public static function saveOriginal($file, $folder = 'uploads/news')
    {
        $uploadPath = public_path($folder);
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $imageName = time() . '_' . uniqid() . '.' . $extension;
        $source = $file->getRealPath() ?: $file->getPathname();
        if (!$source || !is_file($source) || !copy($source, $uploadPath . DIRECTORY_SEPARATOR . $imageName)) {
            throw new \RuntimeException('Uploaded image could not be copied to the public uploads folder.');
        }

        $relativePath = '/' . trim($folder, '/') . '/' . $imageName;
        if (!file_exists(public_path(ltrim($relativePath, '/')))) {
            throw new \RuntimeException('Uploaded image was not found after saving.');
        }

        return $relativePath;
    }

    /**
     * ✅ Optimize and Save Image using Intervention Image (Auto-Compress to 50KB)
     */
    public static function optimizeAndSave($file, $folder = 'uploads/news', $options = [])
    {
        try {
            $defaults = [
                'max_width' => 1200,
                'max_height' => 800,
                'quality' => 60,
                'max_size_kb' => 50,
                'webp' => true,
                'watermark' => false,
            ];
            
            $options = array_merge($defaults, $options);
            
            // ✅ Folder create
            $uploadPath = public_path($folder);
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            // Always write a fresh WebP so the final size is predictable across JPEG/PNG uploads.
            $extension = $options['webp'] ? 'webp' : strtolower($file->getClientOriginalExtension());
            $imageName = time() . '_' . uniqid() . '.' . $extension;
            $fullPath = $uploadPath . '/' . $imageName;

            Log::info("📁 Saving image to: {$fullPath}");

            $targetBytes = (int) ($options['max_size_kb'] * 1024);
            $maxWidth = (int) $options['max_width'];
            $maxHeight = (int) $options['max_height'];
            $qualitySteps = [
                min(85, (int) $options['quality'] + 15),
                (int) $options['quality'], 55, 45, 35, 28, 22, 16, 10,
            ];
            $scales = [1, 0.9, 0.8, 0.7, 0.6, 0.5, 0.4];

            foreach ($scales as $scale) {
                foreach ($qualitySteps as $quality) {
                    $img = Image::make($file)->orientate();
                    $width = $img->width();
                    $height = $img->height();
                    $scaleWidth = (int) round($maxWidth * $scale);
                    $scaleHeight = (int) round($maxHeight * $scale);

                    if ($width > $scaleWidth || $height > $scaleHeight) {
                        $img->resize($scaleWidth, $scaleHeight, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    $img->encode($extension, $quality)->save($fullPath);
                    $size = filesize($fullPath);
                    unset($img);

                    if ($size <= $targetBytes) {
                        Log::info("✅ Image saved: {$folder}/{$imageName}, Final Size: " . round($size / 1024, 2) . ' KB, quality: ' . $quality);
                        return '/' . $folder . '/' . $imageName;
                    }
                }
            }

            throw new \RuntimeException('Unable to compress image below the configured size limit.');
            
        } catch (\Exception $e) {
            Log::error('❌ Image optimization failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * ✅ Reduce Image Size (Loop until target size reached)
     */
    private static function reduceImageSize($path, $targetSizeKB, $extension = 'webp')
    {
        try {
            $quality = 60;
            $attempts = 0;
            
            while (filesize($path) / 1024 > $targetSizeKB && $quality > 5 && $attempts < 10) {
                $quality = $quality - 5;
                $img = Image::make($path);
                $img->encode($extension, $quality);
                $img->save($path);
                $attempts++;
                Log::info("🔄 Reducing quality to {$quality}, size: " . (filesize($path) / 1024) . " KB");
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Image size reduction failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ✅ Delete Image
     */
    public static function delete($path)
    {
        try {
            if (empty($path)) {
                return false;
            }
            
            $cleanPath = ltrim($path, '/');
            $fullPath = public_path($cleanPath);
            
            if (file_exists($fullPath) && is_file($fullPath)) {
                unlink($fullPath);
                Log::info('✅ Image deleted: ' . $path);
                return true;
            }
            
            Log::warning('⚠️ Image not found for deletion: ' . $path);
            return false;
        } catch (\Exception $e) {
            Log::error('❌ Image delete failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ✅ Get Image URL
     */
    public static function getUrl($path)
    {
        if (empty($path)) {
            return null;
        }
        
        $cleanPath = ltrim($path, '/');
        if (file_exists(public_path($cleanPath))) {
            return asset($path);
        }
        
        return null;
    }
    
    /**
     * ✅ Check if image exists
     */
    public static function exists($path)
    {
        if (empty($path)) {
            return false;
        }
        
        $cleanPath = ltrim($path, '/');
        return file_exists(public_path($cleanPath)) && is_file(public_path($cleanPath));
    }
}