<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RechargeController;
use App\Http\Controllers\Api\SetPinNumberController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/get-all-users', [UserController::class, 'index']);
    Route::get('/get-user/{id}', [UserController::class, 'show']);
    Route::patch('/set-pin', [SetPinNumberController::class, 'store']);
    Route::post('/recharge', [RechargeController::class, 'store']);
    Route::get('/get-recharges', [RechargeController::class, 'index']);
    Route::get('/get-balance/{id}', [RechargeController::class, 'show']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
});
