<?php
try {
    require __DIR__.'/../vendor/autoload.php';
    echo "Autoload OK<br>";
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "Bootstrap OK<br>";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "FILE: " . $e->getFile() . "<br>";
    echo "LINE: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "FATAL: " . $e->getMessage() . "<br>";
    echo "FILE: " . $e->getFile() . "<br>";
    echo "LINE: " . $e->getLine() . "<br>";
}