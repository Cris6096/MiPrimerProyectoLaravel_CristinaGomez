<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\EventTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddressController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::controller(UserController::class)->group(function(){
    Route::post('register', 'register');
    Route::get('user/{user}', 'show');
    Route::get('user/{user}/address', 'show_address');
    Route::post('user/{user}/event/{event}/book', 'bookEvent');
    Route::get('user/{user}/event', 'listEvents');
});

Route::controller(AddressController::class)->group(function() {
    Route::post('address', 'store');
    Route::get('address/{address}', 'show');
    Route::get('address/{address}/user', 'show_user');
});

Route::controller(EventController::class)->group(function() {
    Route::post('event', 'store');
    Route::post('event/{event}', 'store');
    Route::get('event', 'index');
    Route::get('event/{event}', 'show');
    Route::get('event/{event}/user', 'listUsers');
});

Route::controller(EventTypeController::class)->group(function() {
    Route::get('type/{type}', 'listEvents');
});

