<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/test-connection', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Connected to Laravel Backend successfully!',
    ]);
});

// Public
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

// Logged-in users only
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::get('/data', [DataController::class, 'index']);
    Route::put('/data/{key}', [DataController::class, 'update']);
    Route::delete('/data/{key}', [DataController::class, 'destroy']);
});
