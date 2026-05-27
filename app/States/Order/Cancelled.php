<?php

namespace App\States\Order;

class Cancelled extends OrderState
{
    public static function label(): string
    {
        return 'cancelled';
    }
}