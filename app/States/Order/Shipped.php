<?php

namespace App\States\Order;

class Shipped extends OrderState
{
    public static function label(): string
    {
        return 'shipped';
    }
}