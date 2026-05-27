<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('payment.{paymentId}', function ($user, $paymentId) {
    return true; // later we secure this
});