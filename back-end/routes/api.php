<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoadingRecordsController;


Route::get('/', function () {
    return response()->json('hello world');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});


Route::get('/permissions', [PermissionController::class, 'index']); // نمایش لیست دسترسی‌ها
Route::post('/permissions', [PermissionController::class, 'store']); // ایجاد دسترسی جدید
Route::get('/permissions/{permission}/users/{search?}', [PermissionController::class, 'getUsers']);
Route::patch('/permissions/{permission}/users/{user}', [PermissionController::class, 'updatePermission']);

Route::get('/users/{user}/permissions', [UserController::class, 'getUserPermissions']);
Route::patch('/users/{user}/permissions/{permission}', [UserController::class, 'updateUserPermission']);

Route::prefix('drivers')->group(function () {
    Route::get('/', [DriverController::class, 'index']);
    Route::get('/all', [DriverController::class, 'all']);
    Route::post('/store', [DriverController::class, 'store']);
    Route::delete('/{id}', [DriverController::class, 'destroy']);
    Route::put('/{driver}', [DriverController::class, 'update']);
});

Route::prefix('trucks')->group(function () {
    Route::get('/', [TruckController::class, 'index']);
    Route::post('/store', [TruckController::class, 'store']);
    Route::get('/all', [TruckController::class, 'all']);
    Route::get('{truck}', [TruckController::class, 'show']);
    Route::put('{truck}', [TruckController::class, 'update']);
    Route::delete('{truck}', [TruckController::class, 'destroy']);
});

Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index']); // لیست کمپانی‌ها
    Route::post('/', [CompanyController::class, 'store']); // ایجاد کمپانی
    Route::put('/{id}', [CompanyController::class, 'update']); // ویرایش کمپانی
    Route::delete('/{id}', [CompanyController::class, 'destroy']); // حذف کمپانی
    Route::prefix('/trucks')->group(function () {
        Route::get('/{id}', [CompanyController::class, 'getTrucks']);
    });
});

Route::prefix('location')->group(function () {
    Route::get('/', [LocationController::class, 'index']); // لیست کمپانی‌ها
    Route::post('/', [LocationController::class, 'store']); // ایجاد کمپانی
    Route::put('/{id}', [LocationController::class, 'update']); // ویرایش کمپانی
    Route::delete('/{id}', [LocationController::class, 'destroy']); // حذف کمپانی
})->middleware('can:LocationMain');

Route::prefix('loading_records')->group(function () {
    Route::get('/', [LoadingRecordsController::class, 'index']);
    Route::get('/count', [LoadingRecordsController::class, 'count']);
    Route::post('/store', [LoadingRecordsController::class, 'store']);
    Route::post('/finalStore', [LoadingRecordsController::class, 'finalStore']);
    Route::get('/pendingAll', [LoadingRecordsController::class, 'pendingAll']);
    Route::delete('/destroy/{id}', [LoadingRecordsController::class, 'destroy']);
    Route::get('/report', [LoadingRecordsController::class, 'report']);
    Route::get('/monthly-count', [LoadingRecordsController::class, 'countPerMonth']);
});
