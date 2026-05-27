<?php

namespace App\States\Order;

class Packed extends OrderState
{
    public static function label(): string
    {
        return 'packed';
    }
}