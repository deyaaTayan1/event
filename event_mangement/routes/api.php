<?php

use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::apiResource('events', EventController::class);

Route::apiResource('events.attendees', AttendeeController::class)
    ->scoped(['attendee' => 'event']);
