<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Get WordPress posts from thepubl2_wp538 database
$posts = DB::connection('mysql')->table('thepubl2_wp538.wpue_posts')
    ->where('post_type', 'post')
    ->where('post_status', 'publish')
    ->whereNotNull('post_name')
    ->get();

echo "Total WordPress posts found: " . $posts->count() . "\n";

$count = 0;
foreach($posts as $post) {
    try {
        DB::table('news')->insert([
            'title' => $post->post_title,
            'slug' => $post->post_name,
            'summary' => substr(strip_tags($post->post_excerpt ?: $post->post_content), 0, 200),
            'body' => $post->post_content,
            'status' => 'published',
            'views' => 0,
            'user_id' => 1,
            'category_id' => 1,
            'published_at' => $post->post_date,
            'created_at' => $post->post_date,
            'updated_at' => $post->post_modified,
        ]);
        $count++;
    } catch(Exception $e) {
        echo "Error for: " . $post->post_name . "\n";
    }
}

echo "News imported successfully: " . $count . "\n";