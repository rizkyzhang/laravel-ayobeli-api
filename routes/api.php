<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\VerifyRequestSignature;
use Illuminate\Support\Facades\Route;

Route::middleware(app()->environment() === 'local' ? [] : [VerifyRequestSignature::class])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });
    Route::apiResource('products', ProductController::class);
});
