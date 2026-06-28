<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $status = $kernel->handle(
        $input = new Symfony\Component\Console\Input\StringInput('config:clear'),
        new Symfony\Component\Console\Output\BufferedOutput
    );
    echo "Config cleared!<br>";
    
    $kernel->handle(
        new Symfony\Component\Console\Input\StringInput('view:clear'),
        new Symfony\Component\Console\Output\BufferedOutput
    );
    echo "Views cleared!<br>";
    echo "Done! Now visit homepage.";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} catch (Error $e) {
    echo "Fatal: " . $e->getMessage();
}