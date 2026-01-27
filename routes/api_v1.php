<?php

use App\Http\Controllers\Api\V1\Workers\Auth\AuthController;
use App\Http\Controllers\Api\V1\Workers\Auth\GeneratePasswordController;
use App\Http\Controllers\Api\V1\Workers\Dashboard\DashboardController;
use App\Http\Controllers\Api\V1\Workers\Profile\AbsenceController;
use App\Http\Controllers\Api\V1\Workers\Profile\ProfileController;
use App\Http\Controllers\Api\V1\Workers\RTWs\RTWsController;
use App\Http\Controllers\Api\V1\Workers\Shift\ShiftController;
use App\Http\Controllers\Api\V1\Workers\Timesheet\TimesheetController;
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
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // RTWs
    Route::get('rtws', [RTWsController::class, 'index']);

    // Shift
    Route::get('confirm-shift', [ShiftController::class, 'index']);
    Route::post('invitation-shift-action', [ShiftController::class, 'invitationShiftAction']);

    // Timesheet
    Route::post('timesheet', [TimesheetController::class, 'index']);

    // Profile
    Route::get('profile', [ProfileController::class, 'index']);
    Route::get('country-option', [ProfileController::class, 'countryOption']);
    Route::post('update-address', [ProfileController::class, 'updateAddress']);
    Route::get('get-update-address-request', [ProfileController::class, 'getUpdateAddressRequest']);

    // Absence
    Route::post('make-my-holiday-request', [AbsenceController::class, 'index']);
    Route::get('get-my-holiday-request', [AbsenceController::class, 'getMyHolidayRequest']);
    Route::post('declined-my-request', [AbsenceController::class, 'declinedHolidayRequest']);
});