<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/recent-orders', [DashboardController::class, 'recentOrders']);
    Route::get('/dashboard/low-stock', [DashboardController::class, 'lowStock']);

    // Categories
    Route::apiResource('categories', CategoryController::class);
    Route::get('/categories/{category}/products', [CategoryController::class, 'products']);

    // Products
    Route::apiResource('products', ProductController::class);
    Route::get('/products/search/query', [ProductController::class, 'search']);
    Route::get('/products/inventory/low-stock', [ProductController::class, 'lowStock']);

    // Customers
    Route::apiResource('customers', CustomerController::class);
    Route::get('/customers/search/query', [CustomerController::class, 'search']);

    // Orders / Sales
    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/{order}/complete', [OrderController::class, 'complete']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::get('/sales/daily', [OrderController::class, 'dailySales']);
    Route::get('/sales/summary', [OrderController::class, 'salesSummary']);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales']);
        Route::get('/inventory', [ReportController::class, 'inventory']);
        Route::get('/customers', [ReportController::class, 'customers']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');