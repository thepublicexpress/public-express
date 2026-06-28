<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\ReporterPoint;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $points_history = ReporterPoint::where('user_id', $user->id)->latest()->take(10)->get();
        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->get();
        
        return view('reporter.wallet', compact('user', 'points_history', 'withdrawals'));
    }

    public function requestWithdrawal(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'amount' => "required|numeric|min:100|max:{$user->wallet_balance}",
            'upi_id' => 'required|string|max:255',
        ]);

        // Check if user has UPI ID
        if (!$request->upi_id && !$user->upi_id) {
            return back()->with('error', 'Please add UPI ID in your profile first.');
        }

        $upiId = $request->upi_id ?: $user->upi_id;

        // Create withdrawal request
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'upi_id' => $upiId,
            'status' => 'pending',
        ]);

        // Deduct from wallet balance immediately
        $user->decrement('wallet_balance', $request->amount);

        // Create notification for admin
        $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'withdrawal_request',
                'title' => '💰 New Withdrawal Request',
                'message' => $user->name . ' requested ₹' . number_format($request->amount, 2) . ' via ' . $upiId,
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Withdrawal request submitted successfully! Amount ₹' . number_format($request->amount, 2) . ' deducted from your wallet.');
    }
}