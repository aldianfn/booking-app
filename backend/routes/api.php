<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\IsOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/user', UserController::class);

    Route::apiResource('/roles', RoleController::class)->middleware(Admin::class);

    Route::apiResource('/hotel', HotelController::class)->except('get')->middleware(IsOwner::class);
});

Route::get('/hotel', [HotelController::class, 'index']);
