<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/tokens', [AuthController::class, 'tokens']);
    });

    Route::prefix('employee')->group(function () {
        Route::post('/check-in', [CheckInController::class, 'checkIn']);
        Route::post('/check-out', [CheckInController::class, 'checkOut']);
        Route::get('/today-status', [CheckInController::class, 'getTodayStatus']);
        Route::get('/history', [CheckInController::class, 'getHistory']);
    });

    Route::prefix('admin')->middleware('role:HR,EMPLOYEE')->group(function () {
        
        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('/chart', [DashboardController::class, 'getAttendanceChart']);
            Route::get('/top-performers', [DashboardController::class, 'getTopPerformers']);
        });

        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'index']);
            Route::post('/', [EmployeeController::class, 'store']);
            Route::get('/{id}', [EmployeeController::class, 'show']);
            Route::put('/{id}', [EmployeeController::class, 'update']);
            Route::delete('/{id}', [EmployeeController::class, 'destroy']);
            Route::get('/{id}/metrics', [EmployeeController::class, 'metrics']);
            Route::post('/bulk-import', [EmployeeController::class, 'bulkImport']);
        });

        Route::prefix('departments')->group(function () {
            Route::get('/', [DepartmentController::class, 'index']);
            Route::post('/', [DepartmentController::class, 'store']);
            Route::get('/{id}', [DepartmentController::class, 'show']);
            Route::put('/{id}', [DepartmentController::class, 'update']);
            Route::delete('/{id}', [DepartmentController::class, 'destroy']);
            Route::get('/{id}/statistics', [DepartmentController::class, 'statistics']);
        });

        Route::prefix('reports')->group(function () {
            Route::get('/monthly', [ReportController::class, 'monthly']);
            Route::get('/export', [ReportController::class, 'export']);
            Route::get('/trends', [ReportController::class, 'trends']);
        });
    });
});