<?php

namespace App\Services;

use Illuminate\Support\Str;

class TranslationService
{
    /**
     * हिंदी टेक्स्ट को इंग्लिश में ऑटोमेटिक ट्रांसलेट करने का फ्री फंक्शन
     */
    public static function translateToEnglish($text)
    {
        if (empty($text)) return '';

        // अगर टेक्स्ट में केवल इंग्लिश अक्षर हैं, तो ट्रांसलेट करने की जरूरत नहीं है
        if (!preg_match('/[^\\x00-\\x7F]/', $text)) {
            return $text;
        }

        try {
            $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=hi&tl=en&dt=t&q=" . urlencode($text);
            
            // गूगल एपीआई से डेटा फेच करना
            $response = file_get_contents($url);
            $result = json_decode($response, true);

            if (isset($result[0][0][0])) {
                return $result[0][0][0]; // ट्रांसलेटेड इंग्लिश टेक्स्ट
            }
        } catch (\Exception $e) {
            // अगर कभी इंटरनेट या गूगल एपीआई डाउन हो तो एरर न आए, सीधे नाम का ही स्लग बन जाए
            return $text;
        }

        return $text;
    }

    /**
     * डायरेक्ट हिंदी टाइटल से क्लीन इंग्लिश स्लग (URL) बनाने का फंक्शन
     */
    public static function createEnglishSlug($hindiTitle)
    {
        // 1. हिंदी से इंग्लिश में बदला (जैसे: "पुलिस मुठभेड़" -> "Police encounter")
        $englishText = self::translateToEnglish($hindiTitle);
        
        // 2. लारावेल स्लग टूल से इसे URL फ्रेंडली बनाया (जैसे: "police-encounter")
        $slug = Str::slug($englishText, '-');

        // 3. अगर स्लग खाली रह जाए तो एक रैंडम यूनिक नंबर जोड़ दें
        if (empty($slug)) {
            $slug = 'news-' . time();
        } else {
            // यूआरएल को और ज्यादा यूनिक बनाने के लिए अंत में टाइमस्टैम्प जोड़ना (जैसा आपके डेटाबेस में है)
            $slug = $slug . '-' . rand(1000, 9999);
        }

        return strtolower($slug);
    }
}