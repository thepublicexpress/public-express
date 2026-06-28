<?php
echo "PHP Works!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Vendor exists: " . (file_exists(__DIR__.'/../vendor/autoload.php') ? 'YES' : 'NO') . "<br>";
echo "Bootstrap exists: " . (file_exists(__DIR__.'/../bootstrap/app.php') ? 'YES' : 'NO') . "<br>";
echo "ENV exists: " . (file_exists(__DIR__.'/../.env') ? 'YES' : 'NO') . "<br>";