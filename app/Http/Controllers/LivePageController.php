<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LivePageController extends Controller
{
    public function livePage($id)
{
    $payment = Payment::findOrFail($id);

    return view('payments.live', compact('payment'));
}
public function status($id)
{
    $payment = Payment::findOrFail($id);

    return response()->json([
        'status' => $payment->status,
        'payment_id' => $payment->id
    ]);
}
}
