<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\AppointmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['api'])->group(function (){
    Route::get('/slots', [SlotController::class, 'available']);
    Route::post('/book', [AppointmentController::class, 'book']);
    Route::delete('/cancel/{id}', [AppointmentController::class, 'cancel']);
});
