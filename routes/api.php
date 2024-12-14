<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\VerifyRequestSignature;
use Illuminate\Support\Facades\Route;

Route::middleware(in_array(app()->environment(), ['local', 'testing'])
    ? [] : [VerifyRequestSignature::class])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });
    Route::apiResource('products', ProductController::class);
});
