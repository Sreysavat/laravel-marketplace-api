<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
     public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        auth()->user()->update([
            'fcm_token' => $request->fcm_token
        ]);

        return response()->json([
            'message' => 'FCM token saved successfully'
        ]);
    }
}
