<?php
// composer-update.php – Run once, then delete.
$projectRoot = dirname(__DIR__);
chdir($projectRoot);

putenv('HOME=' . sys_get_temp_dir());

echo "<pre>";
echo "📦 Running composer update...\n\n";

exec('php composer.phar update 2>&1', $output, $returnCode);
echo "Return code: " . $returnCode . "\n\n";
echo implode("\n", $output);
echo "</pre>";