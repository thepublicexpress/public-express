<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\Notification;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with('user')->latest()->paginate(20);
        return view('admin.withdrawals.index', compact('withdrawals'));
    }
    
    public function process(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,rejected',
            'admin_note' => 'nullable|string|max:1000',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $oldStatus = $withdrawal->status;
        
        // If completed, deduct from wallet balance (already deducted when requested)
        if ($request->status == 'completed' && $oldStatus != 'completed') {
            // Already deducted when request was made
            // Send notification
            Notification::create([
                'user_id' => $withdrawal->user_id,
                'type' => 'withdrawal_completed',
                'title' => '💰 Withdrawal Completed!',
                'message' => 'Your withdrawal of ₹' . number_format($withdrawal->amount, 2) . ' has been completed. Transaction ID: ' . ($request->transaction_id ?? 'N/A'),
                'is_read' => false,
            ]);
        }
        
        // If rejected, refund the amount back to wallet
        if ($request->status == 'rejected' && $oldStatus != 'rejected') {
            $withdrawal->user->increment('wallet_balance', $withdrawal->amount);
            Notification::create([
                'user_id' => $withdrawal->user_id,
                'type' => 'withdrawal_rejected',
                'title' => '❌ Withdrawal Rejected',
                'message' => 'Your withdrawal of ₹' . number_format($withdrawal->amount, 2) . ' has been rejected. Amount refunded to your wallet.',
                'is_read' => false,
            ]);
        }

        $withdrawal->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
            'transaction_id' => $request->transaction_id,
        ]);

        $statusMessage = $request->status == 'completed' ? 'approved' : $request->status;
        return back()->with('success', "Withdrawal request {$statusMessage} successfully.");
    }
}