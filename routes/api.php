<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RoomBillController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Sistem Manajemen Kos
|--------------------------------------------------------------------------
*/

// ============================================================
// AUTH - Public (tidak perlu token)
// ============================================================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// ============================================================
// Protected Routes (wajib login dengan Sanctum token)
// ============================================================
Route::middleware('auth:sanctum')->group(function () {

    // AUTH
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // TENANTS
    Route::apiResource('tenants', TenantController::class);

    // ROOMS
    Route::apiResource('rooms', RoomController::class);

    // RENTALS
    Route::put('/rentals/{rental}/checkout', [RentalController::class, 'checkout']);
    Route::apiResource('rentals', RentalController::class);

    // ROOM BILLS
    Route::post('/room-bills/generate', [RoomBillController::class, 'generate']);
    Route::get('/room-bills', [RoomBillController::class, 'index']);
    Route::get('/room-bills/{roomBill}', [RoomBillController::class, 'show']);

    // PAYMENTS
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::post('/payments', [PaymentController::class, 'store']);
});
