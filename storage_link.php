// public_html/storage_link.php (नई File बनाएँ)
<?php
$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

// Check if symlink function is available
if (!function_exists('symlink')) {
    die('symlink() function is not available on this server.');
}

// Remove old link if exists
if (is_link($link) || file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
        echo "Old symlink removed.<br>";
    } else if (is_dir($link)) {
        echo "Warning: '$link' is a directory, not a symlink. Please remove it manually.<br>";
    }
}

// Create new symlink
if (symlink($target, $link)) {
    echo "✅ Storage symlink created successfully!<br>";
    echo "Target: " . $target . "<br>";
    echo "Link: " . $link . "<br>";
} else {
    echo "❌ Failed to create symlink.<br>";
    echo "Please try the copy method below.";
}
?>