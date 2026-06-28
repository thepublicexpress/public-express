<?php
$host = 'localhost';
$db   = 'thepubl2_tpe';
$user = 'thepubl2_thepublicexpress';
$pass = 'Sariyadav@1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "DB Connected Successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}