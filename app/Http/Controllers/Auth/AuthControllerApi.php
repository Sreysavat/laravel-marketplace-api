<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class AuthControllerApi extends Controller
{
    //api register and login
    public function registerApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $data = $request->all();
        if($request->hasFile('image')){
            $path = $request->file('image')->store('users', 'public');
            $data['image'] = $path;
        }
        $pass = Hash::make($request->password);
       $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $pass,
            'image' => $data['image'] ?? null,
        ]);
         $user->assignRole('customer'); // Assign customer role
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
            ], 200);

    }
    public function loginApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'message' => 'Invalid Credentials'//for api
                ], 401);
                
        }
       
         $user = User::where('email', $request->email)->first();

    $token = $user->createToken('marketplace-api')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => $user,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getPermissionNames(),
        'vendor' => $user->vendor ?? null,
        'token' => $token,
    ]);

    }
    public function logoutApi(Request $request)
    {
         $request->user()->tokens()->delete();
         return response()->json([
            'message' => 'Logout successful'
            ], 200);
    }

    public function vendorRegisterApi(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);
    $data = $request->all();
        if($request->hasFile('image')){
            $path = $request->file('image')->store('users', 'public');
            $data['image'] = $path;
        }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'image' => $data['image'] ?? null,
    ]);

    // assign vendor role
    $user->assignRole('vendor');

    // create vendor profile
    $user->vendor()->create([
        'business_name' => $request->business_name ?? $request->name,
        'status' => 'pending'
    ]);

    return response()->json([
        'message' => 'Vendor registered successfully',
        'user' => $user
    ]);
}
}
