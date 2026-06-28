<?php
// app/Helpers/SmsHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class SmsHelper
{
    /**
     * Send SMS via FREE methods
     */
    public static function sendOtp($mobile, $otp)
    {
        // Method 1: Email-to-SMS Gateway (100% Free)
        $sent = self::sendViaEmailGateway($mobile, $otp);
        
        if ($sent) {
            return true;
        }
        
        // Method 2: Free SMS API (if configured)
        $sent = self::sendViaFreeApi($mobile, $otp);
        
        return $sent;
    }

    /**
     * Send via FREE Email-to-SMS Gateway
     */
    private static function sendViaEmailGateway($mobile, $otp)
    {
        try {
            // Free SMS gateways for Indian numbers
            $gateways = [
                "{$mobile}@sms.indiannumber.com",
                "{$mobile}@txt.att.net",
                "{$mobile}@tmomail.net",
                "{$mobile}@vtext.com",
                "{$mobile}@msg.fi",
            ];
            
            $subject = "Your OTP for The Public Express";
            $message = "Your OTP is: {$otp}\n\nValid for 10 minutes.\n\n- The Public Express";
            
            $headers = "From: noreply@thepublicexpress.com\r\n";
            $headers .= "Reply-To: support@thepublicexpress.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            foreach ($gateways as $to) {
                if (@mail($to, $subject, $message, $headers)) {
                    Log::info("SMS sent via email gateway to {$mobile}");
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("Email gateway SMS failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via FREE SMS API (TextLocal/Fast2SMS)
     */
    private static function sendViaFreeApi($mobile, $otp)
    {
        $apiKey = env('SMS_API_KEY');
        
        if (!$apiKey) {
            Log::info("SMS API key not configured. Skipping.");
            return false;
        }
        
        try {
            $message = "Your OTP is: {$otp}. Valid for 10 minutes. - The Public Express";
            
            // TextLocal API
            $url = "https://api.textlocal.in/send/";
            $data = [
                'apikey' => $apiKey,
                'numbers' => '91' . $mobile,
                'sender' => 'PUBLIC',
                'message' => $message,
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] === 'success') {
                Log::info("SMS sent via TextLocal to {$mobile}");
                return true;
            }
            
            Log::error("TextLocal SMS failed: " . json_encode($result));
            return false;
        } catch (\Exception $e) {
            Log::error("Free API SMS failed: " . $e->getMessage());
            return false;
        }
    }
}