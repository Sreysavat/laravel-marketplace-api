<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Payment;
use App\Events\PaymentStatusUpdated;

   class PaymentService
{
    public function markAsPaid(Payment $payment, array $payload)
{
    if ($payment->is_processed) return;

    $payment->update([
        'status' => 'paid',
        'transaction_id' => data_get($payload, 'data.hash'),
        'paid_at' => now(),
        'is_processed' => true,
        'webhook_payload' => $payload,
    ]);

    $payment->order->update([
        'status' => 'paid'
    ]);

    event(new PaymentStatusUpdated($payment->fresh()));
}
}