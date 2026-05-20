<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorApplication;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;  
class AdminVendorApplicationController extends Controller
{
    public function index()
    {
        return VendorApplication::with('user')->latest()->get();
    }
    public function approve($id)
{
    DB::beginTransaction();

    try {

        $application = VendorApplication::findOrFail($id);

        // 1. prevent double approval
        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Application already processed'
            ], 400);
        }

        $user = $application->user;

        // 2. update application status
        $application->update([
            'status' => 'approved'
        ]);

        // 3. assign vendor role
        $user->assignRole('vendor');

        // 4. create vendor record (STORE)
        $vendor = Vendor::firstOrCreate(
            ['user_id' => $user->id],
            [
                'business_name' => $application->business_name,
                'phone' => $application->phone,
                'address' => $application->address,
                'status' => 'active'
            ]
        );

        DB::commit();

        return response()->json([
            'message' => 'Vendor approved successfully',
            'vendor' => $vendor
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Error approving vendor',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function reject($id)
{
    $application = VendorApplication::findOrFail($id);

    if ($application->status !== 'pending') {
        return response()->json([
            'message' => 'Application already processed'
        ], 400);
    }

    $application->update([
        'status' => 'rejected'
    ]);

    return response()->json([
        'message' => 'Vendor rejected successfully'
    ]);
}
}
