<?php

use App\Http\Controllers\Api\Workers\Auth\AuthController;
use App\Http\Controllers\Api\Workers\Auth\GeneratePasswordController;
use App\Http\Controllers\Api\Workers\RTWs\RTWsController;
use App\Http\Controllers\Api\Workers\Shift\ShiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// WORKER AUTH
Route::post('generate-worker-password-link', [GeneratePasswordController::class, 'generatePasswordLink']);
Route::post('worker-login', [AuthController::class, 'index']);

Route::middleware(['auth:api', 'validate.token'])->group(function () {
    // RTWs
    Route::get('rtws', [RTWsController::class, 'index']);

    // Shift
    Route::get('confirm-shift', [ShiftController::class, 'index']);
});