<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// SPA Routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

// API Routes
Route::prefix('api')->group(function () {
    // Auth Routes
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    
    // Ticket Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/tickets', [App\Http\Controllers\TicketController::class, 'index']);
        Route::post('/tickets', [App\Http\Controllers\TicketController::class, 'store']);
        Route::post('/tickets/{ticket}/classify', [App\Http\Controllers\TicketController::class, 'classify']);
    });
});
