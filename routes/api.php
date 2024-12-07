<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\VerifyRequestSignature;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
Route::apiResource('products', ProductController::class)->middleware(VerifyRequestSignature::class);

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
//    Route::get('sign-request', [AuthController::class, 'signRequest']);
//    Route::get('verify-request', [AuthController::class, 'verifyRequest']);
});
