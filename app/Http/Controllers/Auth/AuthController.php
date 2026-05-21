<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller; 

class AuthController extends Controller
{
    public function loginview()
    {
        return view('auth.login');
    }
     public function registerview()
    {
        return view('auth.register');
    }
    // customer register
    public function register(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $data = $request->all();

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('users', 'public');
        $data['image'] = $path;
    }

     $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'image' => $data['image'] ?? null,
    ]);
    
    $user->assignRole('customer'); // Assign customer role
    return redirect('/login');
}

// vendor register

    public function vendorRegister(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $user->assignRole('vendor'); // Assign vendor role

    return redirect('/login');
}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if(!Auth::attempt($request->only('email', 'password'))){     
            return back()->with(
            'fail', 'Invalid Credentials');
        }
     $request->session()->regenerate();

    $user = Auth::user();

    // SUPER ADMIN
    if ($user->hasRole('super-admin')) {
        return redirect('/admin/dashboard');
    }

    // VENDOR
    if ($user->hasRole('vendor')) {
        return redirect('/vendor/dashboard');
    }

    // CUSTOMER (default)
    return redirect('/dashboard');
}
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

}
