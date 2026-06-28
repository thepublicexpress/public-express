<?php
// लारावेल को बाईपास करके सीधे डेटाबेस को रिपेयर करना
echo "<h2>द पब्लिक एक्सप्रेस - डेटाबेस रिपेयर टूल (Add deleted_at)</h2>";

$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $config = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $config[trim($parts[0])] = trim($parts[1], " '\"");
        }
    }
    
    $host = $config['DB_HOST'] ?? '127.0.0.1';
    $db   = $config['DB_DATABASE'] ?? '';
    $user = $config['DB_USERNAME'] ?? '';
    $pass = $config['DB_PASSWORD'] ?? '';
    
    try {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [<?php
echo "<h2>द पब्लिक एक्सप्रेस - स्टेट मॉडल रिकवरी टूल</h2>";

$modelPath = __DIR__ . '/../app/Models/State.php';

// चेक करते हैं कि Models फोल्डर मौजूद है या नहीं
$dir = dirname($modelPath);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// एकदम क्लीन और परफेक्ट State.php मॉडल कोड
$stateModelCode = "<?php\n\n" .
                  "namespace App\Models;\n\n" .
                  "use Illuminate\Database\Eloquent\Model;\n\n" .
                  "class State extends Model\n" .
                  "{\n" .
                  "    protected \$table = 'states';\n\n" .
                  "    protected \$fillable = [\n" .
                  "        'name', 'slug', 'is_active'\n" .
                  "    ];\n" .
                  "}\n";

try {
    file_put_contents($modelPath, $stateModelCode);
    echo "<b style='color:green; font-size:16px;'>1. सफलता: app/Models/State.php फ़ाइल सफलतापूर्वक बना दी गई है!</b><br>";
} catch (\Exception $e) {
    echo "<b style='color:red;'>त्रुटि: File Write Failed:</b> " . $e->getMessage() . "<br>";
}

echo "<br><span style='color:green; font-size:18px;'><b>जितेंद्र भाई, स्टेट मॉडल बन चुका है! अब कैशे क्लियर लिंक चलाएं।</b></span>";
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
        // चेक करते हैं कि कॉलम पहले से है या नहीं, अगर नहीं है तो जोड़ेंगे
        $check = $pdo->query("SHOW COLUMNS FROM `news_categories` LIKE 'deleted_at'")->fetch();
        
        if (!$check) {
            $pdo->exec("ALTER TABLE `news_categories` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
            echo "<b style='color:green; font-size:16px;'>1. सफलता: news_categories टेबल में 'deleted_at' कॉलम सफलतापूर्वक जोड़ दिया गया है!</b><br>";
        } else {
            echo "<b style='color:blue; font-size:16px;'>सूचना: कॉलम पहले से मौजूद है।</b><br>";
        }
        
    } catch (\Exception $dbEx) {
        echo "<b style='color:red;'>डेटाबेस त्रुटि:</b> " . $dbEx->getMessage() . "<br>";
    }
} else {
    echo "त्रुटि: .env फ़ाइल नहीं मिली।<br>";
}

echo "<br><span style='color:green; font-size:18px;'><b>जितेंद्र भाई, डेटाबेस रिपेयर हो चुका है! अब कैशे साफ़ करने की लिंक चलाएं।</b></span>";