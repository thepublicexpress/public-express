<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ImportWordPressData extends Command
{
    protected $signature = 'import:wordpress';
    protected $description = 'Import all data from WordPress database';

    public function handle()
    {
        $this->info('=====================================');
        $this->info('Starting WordPress Data Import');
        $this->info('=====================================');

        // Check if WordPress database connection works
        try {
            DB::connection('wordpress')->getPdo();
            $this->info('✓ Connected to WordPress database');
        } catch (\Exception $e) {
            $this->error('✗ Cannot connect to WordPress database');
            $this->error('Please check your .env DB_WP_* settings');
            return;
        }

        // 1. Import Categories
        $this->importCategories();
        
        // 2. Import Users (Reporters)
        $this->importUsers();
        
        // 3. Import News
        $this->importNews();
        
        $this->info('=====================================');
        $this->info('Import completed successfully!');
        $this->info('=====================================');
    }

    protected function importCategories()
    {
        $this->info('Importing Categories...');
        
        try {
            $wpCategories = DB::connection('wordpress')->table('terms')
                ->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
                ->where('term_taxonomy.taxonomy', 'category')
                ->select('terms.*')
                ->get();

            $count = 0;
            foreach ($wpCategories as $cat) {
                NewsCategory::updateOrCreate(
                    ['slug' => $cat->slug],
                    [
                        'name' => $cat->name,
                        'name_hi' => $cat->name,
                        'is_active' => true,
                    ]
                );
                $count++;
            }
            $this->info("✓ {$count} categories imported");
        } catch (\Exception $e) {
            $this->error("Error importing categories: " . $e->getMessage());
        }
    }

    protected function importUsers()
    {
        $this->info('Importing Users/Reporters...');
        
        try {
            $wpUsers = DB::connection('wordpress')->table('users')->get();

            $count = 0;
            foreach ($wpUsers as $wpUser) {
                User::updateOrCreate(
                    ['email' => $wpUser->user_email],
                    [
                        'name' => $wpUser->display_name,
                        'phone' => null,
                        'password' => Hash::make('password123'),
                        'role_id' => 4,
                        'is_active' => true,
                    ]
                );
                $count++;
            }
            $this->info("✓ {$count} users imported");
        } catch (\Exception $e) {
            $this->error("Error importing users: " . $e->getMessage());
        }
    }

    protected function importNews()
    {
        $this->info('Importing News...');
        
        try {
            $wpPosts = DB::connection('wordpress')->table('posts')
                ->where('post_type', 'post')
                ->where('post_status', 'publish')
                ->get();

            $count = 0;
            foreach ($wpPosts as $post) {
                // Get category
                $categoryId = null;
                $wpTerm = DB::connection('wordpress')->table('term_relationships')
                    ->where('object_id', $post->ID)
                    ->first();
                
                if ($wpTerm) {
                    $wpTermTax = DB::connection('wordpress')->table('term_taxonomy')
                        ->where('term_taxonomy_id', $wpTerm->term_taxonomy_id)
                        ->first();
                    if ($wpTermTax) {
                        $wpTermName = DB::connection('wordpress')->table('terms')
                            ->where('term_id', $wpTermTax->term_id)
                            ->first();
                        if ($wpTermName) {
                            $category = NewsCategory::where('slug', $wpTermName->slug)->first();
                            if ($category) {
                                $categoryId = $category->id;
                            }
                        }
                    }
                }

                // Get featured image
                $featuredImage = null;
                $thumbnailId = DB::connection('wordpress')->table('postmeta')
                    ->where('post_id', $post->ID)
                    ->where('meta_key', '_thumbnail_id')
                    ->first();
                
                if ($thumbnailId) {
                    $imagePost = DB::connection('wordpress')->table('posts')
                        ->where('ID', $thumbnailId->meta_value)
                        ->first();
                    if ($imagePost) {
                        $featuredImage = $imagePost->guid;
                    }
                }

                News::updateOrCreate(
                    ['slug' => $post->post_name],
                    [
                        'title' => $post->post_title,
                        'summary' => substr(strip_tags($post->post_excerpt ?: $post->post_content), 0, 200),
                        'body' => $post->post_content,
                        'category_id' => $categoryId,
                        'featured_image' => $featuredImage,
                        'status' => 'published',
                        'views' => 0,
                        'published_at' => $post->post_date,
                        'created_at' => $post->post_date,
                        'updated_at' => $post->post_modified,
                    ]
                );
                $count++;
                
                if ($count % 50 == 0) {
                    $this->info("   ... {$count} news imported");
                }
            }
            $this->info("✓ {$count} news imported");
        } catch (\Exception $e) {
            $this->error("Error importing news: " . $e->getMessage());
        }
    }
}