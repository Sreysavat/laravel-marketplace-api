<?php

namespace App\States\Order;

class Delivered extends OrderState
{
    public static function label(): string
    {
        return 'delivered';
    }
}