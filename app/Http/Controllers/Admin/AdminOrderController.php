<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
class AdminOrderController extends Controller
{
    //
     public function index()
    {
        return response()->json(
            Order::with('user')->latest()->paginate(10)
        );
    }
}
