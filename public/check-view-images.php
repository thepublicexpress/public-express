<?php
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

echo "<h2>द पब्लिक एक्सप्रेस - व्यू इमेज ट्रैकर</h2>";

try {
    // 1. होमपेज की ब्लेड फाइल को पढ़ते हैं कि उसमें फोटो का कोड क्या है
    $viewPath = __DIR__.'/../resources/views/home.blade.php';
    echo "<b>1. होमपेज फ़ाइल की जांच:</b><br>";
    if (file_exists($viewPath)) {
        $viewContent = file_get_contents($viewPath);
        
        // चेक करते हैं कि कोड में featured_image कैसे लिखा है
        if (preg_match_all('/src="([^"]*featured_image[^"]*)"/i', $viewContent, $matches)) {
            echo "होमपेज कोड में इमेज का टैग इस तरह लिखा मिला है:<br><pre>";
            foreach ($matches[0] as $match) {
                echo htmlspecialchars($match) . "\n";
            }
            echo "</pre>";
        } else {
            echo "होमपेज कोड में सीधे 'featured_image' नहीं मिला, शायद यह किसी कार्ड या लेआउट फ़ाइल में हो।<br>";
        }
    } else {
        echo "resources/views/home.blade.php फ़ाइल नहीं मिली।<br>";
    }

    // 2. डेटाबेस से एक खबर लेकर उसके असली यूआरएल टेस्ट करते हैं
    $news = DB::table('news')->whereNotNull('featured_image')->first();
    if ($news) {
        $img = $news->featured_image; // news/2026/01/filename.jpg
        echo "<br><b>2. लाइव यूआरएल टेस्ट (खबर: " . htmlspecialchars($news->title) . "):</b><br>";
        echo "डेटाबेस वैल्यू: <code>" . htmlspecialchars($img) . "</code><br><br>";
        
        // संभावित यूआरएल जो लारावेल बना सकता है
        $url1 = asset($img);
        $url2 = asset('storage/' . $img);
        $url3 = url('storage/news/' . str_replace('news/', '', $img));
        
        // इनके फिजिकल रास्ते सर्वर पर
        $p1 = public_path($img);
        $p2 = public_path('storage/' . $img);
        $p3 = storage_path('app/public/' . str_replace('news/', '', $img));

        echo "<b>संभावना A:</b> यदि कोड सीधे एसेट पढ़ रहा है:<br>";
        echo "वेबसाइट लिंक: <a href='$url1' target='_blank'>$url1</a><br>";
        echo "सर्वर पर फोटो यहाँ होनी चाहिए: <code>$p1</code><br>";
        echo "क्या फोटो यहाँ है?: " . (file_exists($p1) ? "<b style='color:green'>हाँ!</b>" : "<b style='color:red'>नहीं</b>") . "<br><br>";

        echo "<b>संभावना B:</b> यदि कोड स्टोरेज के साथ पढ़ रहा है:<br>";
        echo "वेबसाइट लिंक: <a href='$url2' target='_blank'>$url2</a><br>";
        echo "सर्वर पर फोटो यहाँ होनी चाहिए: <code>$p2</code><br>";
        echo "क्या फोटो यहाँ है?: " . (file_exists($p2) ? "<b style='color:green'>हाँ!</b>" : "<b style='color:red'>नहीं</b>") . "<br><br>";
        
        echo "<b>संभावना C:</b> ओरिजिनल स्टोरेज लोकेशन:<br>";
        echo "सर्वर पर फोटो यहाँ है?: " . (file_exists(__DIR__.'/../storage/app/public/' . $img) ? "<b style='color:green'>हाँ!</b>" : "<b style='color:red'>नहीं</b>") . "<br>";
    }

} catch (\Exception $e) {
    echo "त्रुटि (Error): " . $e->getMessage();
}