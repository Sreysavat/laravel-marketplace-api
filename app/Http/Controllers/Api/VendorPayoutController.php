<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorPayoutController extends Controller
{
     public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
        ]);

        $vendor = Auth::user();

        $wallet = $vendor->wallet;

       if (!$wallet) {

    $wallet = $vendor->wallet()->create([
        'balance' => 0,
        'pending_balance' => 0
    ]);
}

        if ($wallet->balance < $request->amount) {
            return response()->json([
                'message' => 'Insufficient balance'
            ], 400);
        }

        $payout = Payout::create([
            'vendor_id' => $vendor->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'status' => 'pending',
        ]);

        $wallet->decrement('balance', $request->amount);
        $wallet->increment('pending_balance', $request->amount);

        return response()->json([
            'message' => 'Payout request submitted',
            'data' => $payout
        ]);
    }

    public function approve($id)
{
    $payout = Payout::find($id);

    if (!$payout) {
        return response()->json([
            'message' => 'Payout not found'
        ], 404);
    }

    $payout->update([
        'status' => 'approved',
        'approved_by' => Auth::id(),
        'approved_at' => now(),
        'paid_at' => now(),
    ]);

    return response()->json([
        'message' => 'Payout approved',
        'data' => $payout
    ]);
}
}
