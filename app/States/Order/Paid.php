<?php

namespace App\States\Order;

class Paid extends OrderState
{
    public static function label(): string
    {
        return 'paid';
    }
}