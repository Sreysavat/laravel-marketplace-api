<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivePageController;
Route::get('home', function () {
    return view('home');
});
Route::get('/login', [AuthController::class, 'loginview']);//login form
Route::get('/register', [AuthController::class, 'registerview']);//register form
//customer
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// admin and vendor login
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post(('/vendor/register'), [AuthController::class, 'vendorRegister'])->name('vendor.register');
Route::post('/vendor/login', [AuthController::class, 'login'])->name('vendor.login');

// login to permission
Route::get('/dashboard',function(){
    return view('dashboard'); })->middleware('auth', 'verified')->name('dashboard');
    Route::get('/admin/dashboard',function(){
    return view('admin'); })->middleware('auth', 'verified',)->name('admin');
    Route::get('/vendor/dashboard',function(){
    return view('vendor'); })->middleware('auth', 'verified',)->name('vendor');

    Route::get('/payments/live/{id}', [LivePageController::class, 'livePage']);
    Route::get('/payments/status/{id}', [LivePageController::class, 'status']);
