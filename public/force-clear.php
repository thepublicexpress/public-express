<?php
define('LARAVEL_START', microtime(true));

// 1. Autoload और App लोड करें
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    die('Vendor autoload missing!');
}
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

echo "<h3>The Public Express - Server Reset</h3>";

// 2. मैन्युअल रूप से bootstrap cache की फाइलें डिलीट करना
$cacheFiles = [
    __DIR__.'/../bootstrap/cache/config.php',
    __DIR__.'/../bootstrap/cache/routes.php',
    __DIR__.'/../bootstrap/cache/services.php',
    __DIR__.'/../bootstrap/cache/packages.php'
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        @unlink($file);
        echo "Deleted Cache File: " . basename($file) . "<br>";
    }
}

// 3. Laravel कर्नल के थ्रू फ्रेश क्लियर कमांड चलाना
try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    // इनपुट और आउटपुट बफ़र सेट करें
    $input = new Symfony\Component\Console\Input\StringInput('config:clear');
    $output = new Symfony\Component\Console\Output\BufferedOutput();
    $kernel->handle($input, $output);
    echo "Laravel Config: Clear Success!<br>";
    
    $input2 = new Symfony\Component\Console\Input\StringInput('cache:clear');
    $kernel->handle($input2, $output);
    echo "Laravel Cache: Clear Success!<br>";

    echo "<br><span style='color:green; font-weight:bold;'>सब कुछ साफ़ हो गया है! अब मुख्य वेबसाइट खोल कर देखिए।</span>";
} catch (\Throwable $e) {
    echo "<br><span style='color:red;'>Laravel Error: " . $e->getMessage() . "</span><br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine();
}