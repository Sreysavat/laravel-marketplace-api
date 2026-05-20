<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;

class AdminVendorController extends Controller
{
    //
    public function index()
    {
        return response()->json(
            Vendor::with('user')->latest()->paginate(10)
        );
    }
}
