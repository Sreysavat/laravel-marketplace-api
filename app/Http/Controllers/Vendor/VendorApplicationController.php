<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorApplication;

class VendorApplicationController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'business_name' => 'required',
            'business_email' => 'required|email',
            'phone' => 'required|string|max:20',
        ]);

        $application = VendorApplication::create([
            'user_id' => auth()->id(),
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Vendor application submitted successfully', 
            'data' => $application
            ], 201);
    }
}
