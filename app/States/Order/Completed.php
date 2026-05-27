<?php

namespace App\States\Order;

class Completed extends OrderState
{
    public static function label(): string
    {
        return 'completed';
    }
}