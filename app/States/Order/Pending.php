<?php

namespace App\States\Order;


class Pending extends OrderState
{
    public static function label(): string
    {
        return 'pending';
    }

}
