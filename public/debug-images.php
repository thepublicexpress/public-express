<?php
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

echo "<h2>द पब्लिक एक्सप्रेस - इमेज पाथ डायग्नोसिस</h2>";

try {
    // 1. डेटाबेस से एक खबर उठाते हैं
    $news = DB::table('news')->whereNotNull('featured_image')->first();
    
    if ($news) {
        $dbImage = $news->featured_image;
        echo "<b>1. डेटाबेस में फोटो का नाम:</b> " . htmlspecialchars($dbImage) . "<br><br>";
        
        // 2. चेक करते हैं कि क्या फोटो सीधे public/storage में है
        $path1 = __DIR__.'/storage/' . $dbImage;
        echo "<b>2. रास्ता 1 (सीधे storage में):</b> " . $path1 . "<br>";
        echo "क्या फाइल यहाँ मौजूद है?: " . (file_exists($path1) ? "<span style='color:green'>हाँ, मिल गई!</span>" : "<span style='color:red'>नहीं मिली</span>") . "<br><br>";
        
        // 3. चेक करते हैं कि क्या फोटो storage/news/ के अंदर है
        $path2 = __DIR__.'/storage/news/' . $dbImage;
        echo "<b>3. रास्ता 2 (storage/news के अंदर):</b> " . $path2 . "<br>";
        echo "क्या फाइल यहाँ मौजूद है?: " . (file_exists($path2) ? "<span style='color:green'>हाँ, मिल गई!</span>" : "<span style='color:red'>नहीं मिली</span>") . "<br><br>";
        
        // 4. ऑटोमैटिक फिक्स (अगर फोटो news फोल्डर में मिली तो डेटाबेस अपडेट कर देंगे)
        if (!file_exists($path1) && file_exists($path2)) {
            echo "<b>[बीमारी पकड़ी गई]:</b> फोटो 'news' फोल्डर के अंदर हैं, लेकिन डेटाबेस में 'news/' लिखना छूट गया है।<br>";
            
            // डेटाबेस में सबके आगे news/ जोड़ देते हैं
            $updated = DB::update("
                UPDATE news 
                SET featured_image = CONCAT('news/', featured_image)
                WHERE featured_image NOT LIKE 'news/%' AND featured_image IS NOT NULL
            ");
            echo "<b style='color:green'>सफलता:</b> कुल " . $updated . " खबरों के पाथ में 'news/' जोड़ दिया गया है! अब जादू देखिये।";
        } elseif (!file_exists($path1) && !file_exists($path2)) {
            echo "<b style='color:red'>[गंभीर समस्या]:</b> फाइल दोनों में से किसी जगह नहीं मिली। कृपया चेक करें कि क्या फोटो सच में `public_html/storage/app/public/news` फोल्डर में ही अपलोड हुई हैं?";
        } else {
            echo "<b style='color:green'>सब सही दिख रहा है!</b> अगर फिर भी फोटो न दिखे तो ब्राउज़र का कैश (Ctrl+F5) साफ करें।";
        }
    } else {
        echo "डेटाबेस में कोई खबर ही नहीं मिली।";
    }

} catch (\Exception $e) {
    echo "त्रुटि (Error): " . $e->getMessage();
}