<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\News;

class ImportWordPressImages extends Command
{
    protected $signature = 'import:images';
    protected $description = 'Import images from WordPress uploads folder';

    public function handle()
    {
        $this->info('=====================================');
        $this->info('Starting WordPress Images Import');
        $this->info('=====================================');

        $news = News::whereNotNull('featured_image')->get();
        $count = 0;
        $failed = 0;
        
        foreach ($news as $item) {
            // Try different possible paths
            $paths = [
                'C:/xampp/htdocs/wordpress/wp-content/uploads/' . basename($item->featured_image),
                'C:/xampp/htdocs/wordpress' . str_replace('https://thepublicexpress.com', '', $item->featured_image),
                'C:/xampp/htdocs/wordpress' . str_replace('http://localhost/wordpress', '', $item->featured_image),
            ];
            
            $oldPath = null;
            foreach ($paths as $path) {
                if (File::exists($path)) {
                    $oldPath = $path;
                    break;
                }
            }
            
            if ($oldPath && File::exists($oldPath)) {
                $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newName = $item->slug . '.' . $extension;
                $newPath = storage_path('app/public/news/' . $newName);
                
                // Create directory if not exists
                if (!File::exists(storage_path('app/public/news'))) {
                    File::makeDirectory(storage_path('app/public/news'), 0755, true);
                }
                
                File::copy($oldPath, $newPath);
                $item->featured_image = 'news/' . $newName;
                $item->save();
                $count++;
                $this->info("✓ Imported: {$item->title}");
            } else {
                $failed++;
                $this->warn("✗ Image not found for: {$item->title}");
            }
        }
        
        $this->info('=====================================');
        $this->info("✓ {$count} images imported successfully");
        $this->info("✗ {$failed} images failed");
        $this->info('=====================================');
    }
}