<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function() {
    Route::post('logout',   [AuthController::class, 'logout']);
    Route::get('me',        [AuthController::class, 'me']);

    // Protected ticket routes:
    Route::post('/tickets',            [TicketController::class, 'store']);
    Route::get('/tickets',             [TicketController::class, 'index']);
    Route::post('/tickets/{ticket}/classify', [TicketController::class, 'classify']);
});
