<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class AdminProductController extends Controller
{
    //
     public function index()
    {
        return response()->json(
            Product::with('store')->latest()->paginate(10)
        );
    }
}
