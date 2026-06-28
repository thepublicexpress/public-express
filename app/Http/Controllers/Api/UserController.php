<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller {
    public function profile() {
        return response()->json(Auth::user()->load('role','state','district','tehsil'));
    }
    
    public function updateProfile(Request $request) {
        $user = Auth::user();
        $data = $request->validate([
            'name'       => 'sometimes|required|min:2',
            'upi_id'     => 'nullable|string|max:50',
            'district_id'=> 'nullable|exists:districts,id',
            'tehsil_id'  => 'nullable|exists:tehsils,id',
        ]);
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars','public');
        }
        $user->update($data);
        return response()->json(['message' => 'Profile updated', 'user' => $user->fresh()]);
    }
    
    public function wallet() {
        $user = Auth::user();
        return response()->json([
            'points'         => $user->points,
            'wallet_balance' => $user->wallet_balance,
            'point_logs'     => $user->pointLogs()->with('news:id,title')->latest()->limit(20)->get(),
            'withdrawals'    => $user->withdrawals()->latest()->limit(10)->get(),
        ]);
    }
    
    public function requestWithdrawal(Request $request) {
        $user = Auth::user();
        $request->validate([
            'amount' => 'required|numeric|min:100|max:'.$user->wallet_balance,
            'upi_id' => 'required|string',
        ]);
        if ($user->wallet_balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 422);
        }
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount'  => $request->amount,
            'upi_id'  => $request->upi_id,
            'status'  => 'pending',
        ]);
        return response()->json(['message' => 'Withdrawal request submitted', 'withdrawal' => $withdrawal], 201);
    }
    
    public function leaderboard() {
        $reporters = \App\Models\User::where('role_id', 4)
            ->where('is_verified_reporter', true)
            ->orderBy('points', 'desc')
            ->limit(20)
            ->get(['id','name','points','avatar','district_id']);
        return response()->json($reporters);
    }
}
