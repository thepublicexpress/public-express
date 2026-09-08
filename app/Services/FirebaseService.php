<?php

namespace App\Services;

use Exception;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $client;
    protected $projectId;
    protected $accessToken;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $credentialsPath = storage_path(env('FIREBASE_CREDENTIALS', 'app/firebase/firebase-credentials.json'));

        if (!file_exists($credentialsPath)) {
            Log::error('Firebase credentials file not found: ' . $credentialsPath);
            throw new Exception('Firebase credentials file not found');
        }

        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            $credentialsPath
        );
        $this->accessToken = $credentials->fetchAuthToken()['access_token'];

        $this->client = new Client([
            'base_uri' => 'https://fcm.googleapis.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * ✅ Clean UTF-8 string - Remove malformed characters
     */
    private function cleanUtf8String($text)
    {
        if (empty($text)) {
            return '';
        }
        
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $text);
        $text = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        return trim($text);
    }

    /**
     * Send push notification using FCM HTTP v1 API
     */
    public function sendNotification($fcmToken, $title, $body, $data = [], $clickAction = null)
    {
        try {
            // ✅ Clean title and body
            $cleanTitle = $this->cleanUtf8String($title);
            $cleanBody = $this->cleanUtf8String($body);
            
            if (empty($cleanTitle)) {
                $cleanTitle = 'द पब्लिक एक्सप्रेस';
            }
            if (empty($cleanBody)) {
                $cleanBody = 'ताजा खबर!';
            }

            // ✅ Convert all data values to STRINGS and clean them
            $stringData = [];
            foreach ($data as $key => $value) {
                $cleanValue = $this->cleanUtf8String((string) $value);
                $stringData[$key] = $cleanValue;
            }

            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $cleanTitle,
                        'body' => $cleanBody,
                    ],
                    'webpush' => [
                        'headers' => ['Urgency' => 'high'],
                        'notification' => [
                            'icon' => asset('images/logo.png'),
                            'badge' => asset('images/badge.png'),
                            'requireInteraction' => true,
                            'vibrate' => [200, 100, 200],
                        ]
                    ],
                    'data' => $stringData,
                ]
            ];

            if ($clickAction) {
                $payload['message']['webpush']['fcm_options'] = [
                    'link' => $this->cleanUtf8String($clickAction)
                ];
            }

            $response = $this->client->post(
                "projects/{$this->projectId}/messages:send",
                ['json' => $payload]
            );

            $result = json_decode($response->getBody(), true);
            Log::info('✅ FCM Notification sent', [
                'token' => substr($fcmToken, 0, 20) . '...',
                'title' => $cleanTitle
            ]);
            return $result;

        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            Log::error('❌ FCM Notification failed: ' . $errorMsg);

            if (str_contains($errorMsg, '404') || str_contains($errorMsg, 'NOT_FOUND')) {
                \App\Models\PushSubscription::where('fcm_token', $fcmToken)->update(['is_active' => false]);
                Log::info('🔴 Token deactivated due to 404', ['token' => substr($fcmToken, 0, 20) . '...']);
            }

            return null;
        }
    }

    /**
     * Send to multiple tokens
     */
    public function sendToMultiple($tokens, $title, $body, $data = [], $clickAction = null)
    {
        $results = [];
        $sentCount = 0;
        
        $cleanTitle = $this->cleanUtf8String($title);
        $cleanBody = $this->cleanUtf8String($body);
        
        foreach ($tokens as $token) {
            if (!empty($token)) {
                $result = $this->sendNotification($token, $cleanTitle, $cleanBody, $data, $clickAction);
                $results[] = $result;
                if ($result !== null) {
                    $sentCount++;
                }
            }
        }
        
        Log::info("📊 FCM: Sent to {$sentCount} of " . count($tokens) . " tokens");
        return $results;
    }
}