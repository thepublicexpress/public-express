<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FCMController extends Controller
{
    public function saveToken(Request $request)
    {
        try {
            $request->validate(['fcm_token' => 'required|string']);
            
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $user->fcm_token = $request->fcm_token;
            $user->fcm_token_updated_at = now();
            $user->save();

            Log::info('✅ FCM token saved', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ FCM token save error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function removeToken(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $user->fcm_token = null;
            $user->fcm_token_updated_at = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'FCM token removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}