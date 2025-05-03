<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/auth', [AuthController::class, 'signIn']);

Route::get('/users/{id}', [UserController::class, 'show']);

Route::post('/users', [UserController::class, 'store']);

Route::patch('/users/password/{id}', [UserController::class, 'updatePassword']);

Route::post('/users/{id}/upload-photo', [UserController::class, 'uploadProfilePhoto']);

Route::patch('/users/{id}/delete-photo', [UserController::class, 'deleteProfilePhoto']);


Route::post('/bookings', [BookingController::class, 'store']);

Route::get('/bookings/my-flights', [BookingController::class, 'showMyFlights']);

Route::get('/bookings/history', [BookingController::class, 'showHistory']);
