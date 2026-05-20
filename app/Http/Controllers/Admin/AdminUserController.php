<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\user;
use Illuminate\Http\Request;
use App\Models\Vendor;

class AdminUserController extends Controller
{
    //
     public function index()
    {
        return response()->json(
            User::with('roles')->latest()->paginate(10)
        );
    }
}
