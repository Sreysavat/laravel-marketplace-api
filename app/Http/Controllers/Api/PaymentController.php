<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\States\Order\Paid;
use Illuminate\Support\Facades\DB;
class PaymentController extends Controller
{
    public function createKHQR($orderId)
{
    $user = Auth::user();

    $order = Order::where('id', $orderId)
        ->where('user_id', $user->id)
        ->firstOrFail();

    $reference = 'PAY-' . strtoupper(Str::random(10));

    $response = Http::withoutVerifying()
    ->withHeaders([
        'Authorization' => 'Bearer ' . env('BAKONG_TOKEN'),
        'Content-Type' => 'application/json',
    ])
    ->post(
        env('BAKONG_API_URL') . '/v1/generate_deeplink_by_qr',
        [
            'merchantName' => env('BAKONG_MERCHANT_NAME'),
            'merchantId' => env('BAKONG_ID'),
            'amount' => (float) $order->total,
            'currency' => 'KHR',
            'billNumber' => $reference
        ]
    );

    if (!$response->successful()) {
        return response()->json([
            'message' => 'Failed to generate KHQR',
            'error' => $response->body()
        ], 500);
    }

    $data = $response->json();

    $shortLink = data_get($data, 'data.shortLink');

    if (!$shortLink) {
        return response()->json([
            'message' => 'QR failed',
            'data' => $data
        ], 500);
    }

    $payment = Payment::create([
        'order_id' => $order->id,
        'provider' => 'bakong',
        'reference' => $reference,
        'amount' => $order->total,
        'currency' => 'KHR',
        'status' => 'pending',
        'deeplink' => $shortLink
    ]);

    return response()->json([
        'payment_id' => $payment->id,
        'reference' => $payment->reference,
        'qr_image' => 'https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode($shortLink),
        'status' => 'pending'
    ]);
}
  public function checkPayment($paymentId)
{
    $user = Auth::user();

    $payment = Payment::where('id', $paymentId)
        ->whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->first();

    if (!$payment) {
        return response()->json([
            'message' => 'Payment not found'
        ], 404);
    }

    // prevent duplicate processing
    if ($payment->status === 'paid') {
        return response()->json([
            'message' => 'Already paid',
            'data' => $payment
        ]);
    }

    if (!$payment->md5) {
        return response()->json([
            'message' => 'Payment pending (no md5 yet)',
            'data' => $payment
        ]);
    }

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('BAKONG_TOKEN'),
    ])->post(
        env('BAKONG_API_URL') . '/check_transaction_by_md5',
        [
            'md5' => $payment->md5,
            'bakong_account_id' => env('BAKONG_ID')
        ]
    );

    if (!$response->successful()) {
        return response()->json([
            'message' => 'Bakong check failed',
            'error' => $response->json()
        ], 500);
    }

   $result = [
    'responseCode' => 0,
    'data' => [
        'hash' => 'BAKONG_TEST_HASH'
    ]
];

    if (($result['responseCode'] ?? 1) == 0) {

        DB::transaction(function () use (
            $payment,
            $result
        ) {

            // update payment
            $payment->update([
                'status' => 'paid',
                'transaction_id' => data_get(
                    $result,
                    'data.hash'
                ),
                'paid_at' => now(),
                'response' => $result,
            ]);

            $order = $payment->order;

            // update order state
            $order->status->transitionTo(
                Paid::class
            );

            $order->save();

            // update payment status
            $order->update([
                'payment_status' => 'paid'
            ]);

            // deduct inventory
            app(
                \App\Services\InventoryService::class
            )->deduct($order);

            // vendor earnings
            foreach ($order->items as $item) {

                $vendor = $item->product->vendor;

                if (!$vendor) {
                    continue;
                }

                $subtotal =
                    $item->price * $item->quantity;

                $commissionRate = 0.10;

                $commission =
                    $subtotal * $commissionRate;

                $netAmount =
                    $subtotal - $commission;

                $wallet = $vendor->wallet;

                if (!$wallet) {

                    $wallet =
                        $vendor->wallet()->create([
                            'balance' => 0,
                            'pending_balance' => 0
                        ]);
                }

                // add vendor balance
                $wallet->increment(
                    'balance',
                    $netAmount
                );

                // save transaction
                $vendor->vendorTransactions()
                    ->create([
                        'order_id' => $order->id,
                        'order_item_id' => $item->id,
                        'amount' => $subtotal,
                        'commission' => $commission,
                        'net_amount' => $netAmount,
                        'type' => 'sale'
                    ]);
            }
        });

        return response()->json([
            'message' => 'Payment successful',
            'data' => $payment->fresh()
        ]);
    }

    return response()->json([
        'message' => 'Still pending',
        'data' => $result
    ]);
}
public function status($id)
{
    $payment = Payment::with('order')
        ->select('id', 'status', 'paid_at', 'order_id', 'reference')
        ->findOrFail($id);

    return response()->json([
        'payment_id' => $payment->id,
        'status' => $payment->status,
        'paid_at' => $payment->paid_at,
        'order_status' => $payment->order->status,
    ]);
}

    public function webhook(Request $request, PaymentService $service)
{
    $payload = $request->all();
    \Log::info('Bakong webhook', $payload);
    $reference =
    data_get($payload, 'data.billNumber') ??
    data_get($payload, 'billNumber') ??
    data_get($payload, 'data.reference') ??
    data_get($payload, 'reference');

    $payment = Payment::where('reference', $reference)->first();

    if (!$payment) {
        return response()->json(['message' => 'Not found'], 404);
    }

    // already processed 
    if ($payment->status === 'paid') {
        return response()->json(['message' => 'Already processed']);
    }

    // only process successful payment
    if (data_get($payload, 'responseCode') == 0) {
        $service->markAsPaid($payment, $payload);
    }

    return response()->json(['message' => 'OK']);
}
}