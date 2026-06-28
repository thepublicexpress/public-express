<?php
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // Make sure app is running
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    echo "Laravel OK<br>";
    
    // Get config values properly
    $dbHost = config('database.connections.mysql.host');
    $dbPort = config('database.connections.mysql.port');
    $dbName = config('database.connections.mysql.database');
    $dbUser = config('database.connections.mysql.username');
    $dbPass = config('database.connections.mysql.password');
    
    echo "DB Host: " . $dbHost . "<br>";
    echo "DB Name: " . $dbName . "<br>";
    echo "DB User: " . $dbUser . "<br>";
    
    // DB Test
    $pdo = new PDO(
        "mysql:host=$dbHost;port=$dbPort;dbname=$dbName",
        $dbUser,
        $dbPass
    );
    echo "DB Connected OK<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'published'");
    $count = $stmt->fetchColumn();
    echo "Total Published News: " . $count . "<br>";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
} catch (Error $e) {
    echo "FATAL: " . $e->getMessage() . "<br>";
}