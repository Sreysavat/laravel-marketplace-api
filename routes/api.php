<?php

use App\Http\Controllers\Auth\AuthControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVendorController;
use App\Http\Controllers\Vendor\VendorApplicationController;
use App\Http\Controllers\Admin\AdminVendorApplicationController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ProductVariantController;
use App\Http\Controllers\Vendor\ProductImageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Api\ProductBrowseController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('vendor/register', [AuthControllerApi::class, 'vendorRegisterApi']);
Route::post('register', [AuthControllerApi::class, 'registerApi']);
Route::post('login', [AuthControllerApi::class, 'loginApi']);
Route::post('logout', [AuthControllerApi::class, 'logoutApi'])->middleware('auth:sanctum');


Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

//admin route
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:super-admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->middleware('permission:analytics.view');

        Route::get('/users', [AdminUserController::class, 'index'])
            ->middleware('permission:users.view');

        Route::get('/vendors', [AdminVendorController::class, 'index'])
            ->middleware('permission:vendor.view');

    });

//
    Route::middleware(['auth:sanctum'])->group(function () {

    // CUSTOMER APPLY
    Route::post('/vendor/apply', [VendorApplicationController::class, 'apply']);

});
// route for approving and rejecting vendor applications
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:super-admin'])
    ->group(function () {

        Route::get('/vendor-applications', [AdminVendorApplicationController::class, 'index']);

        Route::post('/vendor-applications/{id}/approve', [AdminVendorApplicationController::class, 'approve']);

        Route::post('/vendor-applications/{id}/reject', [AdminVendorApplicationController::class, 'reject']);
    });
//Route products
Route::middleware(['auth:sanctum', 'role:vendor'])->prefix('vendor')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    
});
//Route for product variants
Route::middleware(['auth:sanctum', 'role:vendor'])
    ->prefix('vendor/products')
    ->group(function () {

        Route::get('{productId}/variants', [ProductVariantController::class, 'index']);
        Route::post('{productId}/variants', [ProductVariantController::class, 'store']);
        Route::get('variant/{id}', [ProductVariantController::class, 'show']);
        Route::put('variant/{id}', [ProductVariantController::class, 'update']);
        Route::delete('variant/{id}', [ProductVariantController::class, 'destroy']);

});
//Route for product images
Route::middleware(['auth:sanctum', 'role:vendor'])
    ->prefix('vendor/products')
    ->group(function () {
         Route::post(
            '/{id}/images',[ProductImageController::class, 'store']);
        Route::get('/{id}/images', [ProductImageController::class, 'index']);
        Route::delete('/product-images/{id}',[ProductImageController::class, 'destroy']);
        Route::post('/product-images/{id}/set-main',[ProductImageController::class, 'setMain']);
    });
    //Route for categories
    Route::middleware(['auth:sanctum'])->group(function () {
    // public access
    Route::get('/categories', [CategoryController::class, 'index']);
    // admin only 
    Route::middleware(['role:super-admin'])->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
});
    //Route for browsing products
    Route::get('/products', [ProductBrowseController::class, 'index']);
    Route::get('/products/{id}', [ProductBrowseController::class, 'show']);
    //Route for cart
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/items/{id}', [CartController::class,'update']);
    Route::delete('/cart/clear', [CartController::class,'clear']);
    Route::delete('/cart/{id}', [CartController::class, 'remove']);
    Route::post('/checkout', [CheckoutController::class,'checkout']);
});
    

    Route::middleware('auth:sanctum')->group(function () {

    Route::post('/orders/{id}/status', [OrderStatusController::class, 'updateStatus']);
    Route::get('/orders/{id}/timeline', [OrderStatusController::class, 'timeline']);

});