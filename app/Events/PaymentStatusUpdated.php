<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('payment.' . $this->payment->id);
    }

    public function broadcastAs(): string
    {
        return 'payment.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'payment_id' => $this->payment->id,
            'status' => $this->payment->status,
            'order_id' => $this->payment->order_id,
            'order_status' => $this->payment->order->status ?? null,
            'paid_at' => $this->payment->paid_at,
        ];
    }
}