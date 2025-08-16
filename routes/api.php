<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingController;

Route::middleware('api_token_auth')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::patch('/bookings/{booking}/slots/{slot}', [BookingController::class, 'updateSlot']);
    Route::post('/bookings/{booking}/slots', [BookingController::class, 'addSlot']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
});
