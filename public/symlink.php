<?php
$target = '../storage/app/public';
$link = 'storage';

if (is_link($link)) {
    echo "✅ Symlink already exists.<br>";
} else {
    if (file_exists($link)) {
        unlink($link);
    }
    symlink($target, $link);
    echo "✅ Symlink created.<br>";
}
echo '<p><a href="/storage/news/2026/01/20210516_102338.jpg">Test Image</a></p>';
echo '<p><a href="/">🏠 Go Home</a></p>';