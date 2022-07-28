<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('layouts.app');
});

// Route::get('/reserva', function () {
//     return view('livewire.reserva');
// });

// Route::middleware(['auth:sanctum', 'verified'])->get('/reserva')->name('reserva');

// Auth:: routes(['register'=>false, 'reset'=>false]);

// Auth::routes(['reset' => false, 'verify' => false]);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/reserva', function () {
        return view('layouts.app');
    })->name('reserva');
});

