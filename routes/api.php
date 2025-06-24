<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RechargeController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/recharge', [RechargeController::class, 'store']);
    Route::get('/get-all-users', [UserController::class, 'index']);
    Route::get('/get-user/{id}', [UserController::class, 'show']);
    Route::post('/transaction', [TransactionController::class, 'store']);
    Route::get('/transaction-list', [TransactionController::class, 'index']);
});
