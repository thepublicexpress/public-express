<?php
$targetFolder = __DIR__.'/../storage/app/public';
$linkFolder = __DIR__.'/storage';

if (file_exists($linkFolder)) {
    // अगर पुराना लिंक गलत बना है तो उसे डिलीट करेंगे
    @unlink($linkFolder);
}

if (symlink($targetFolder, $linkFolder)) {
    echo "<h2 style='color:green;'>बधाई हो जितेंद्र भाई! स्टोरेज लिंक सफ़लतापूर्वक बन गया है।</h2>";
} else {
    echo "<h2 style='color:red;'>त्रुटि: लिंक नहीं बन पाया।</h2>";
}