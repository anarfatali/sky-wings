<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\FlightController;
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

Route::post('/auth/signIn', [AuthController::class, 'signIn']);

Route::get('/users/{id}', [UserController::class, 'show']);

Route::post('/users', [UserController::class, 'store']);

Route::patch('/users/password', [UserController::class, 'updatePassword']);

Route::patch('/users/email', [UserController::class, 'updateEmail']);

Route::post('/users/{id}/upload-photo', [UserController::class, 'uploadProfilePhoto']);

Route::patch('/users/{id}/delete-photo', [UserController::class, 'deleteProfilePhoto']);


Route::post('/bookings', [BookingController::class, 'store']);

Route::get('/bookings/my-flights', [BookingController::class, 'showMyFlights']);

Route::get('/bookings/history', [BookingController::class, 'showHistory']);


Route::get('flights', [FlightController::class, 'index']);

Route::get('flights/{id}', [FlightController::class, 'show']);

Route::post('flights/search', [FlightController::class, 'search']);

Route::post('flights', [FlightController::class, 'store']);

Route::put('flights/{id}', [FlightController::class, 'update']);

Route::delete('flights/{id}', [FlightController::class, 'destroy']);


Route::post('airports', [AirportController::class, 'store']);

Route::get('airports', [AirportController::class, 'getAll']);


Route::get("enum/cities", [EnumController::class, 'cities']);

Route::get("enum/aircrafts", [EnumController::class, 'aircrafts']);


Route::post("admin", [AdminController::class, 'store']);

Route::get("admin", [AdminController::class, 'getAdmins']);

Route::get("admin/{id}", [AdminController::class, 'getAdmin']);

Route::post('admin/signIn', [AdminController::class, 'signIn']);
