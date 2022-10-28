<?php

use App\Http\Livewire\Reserva;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Listarreservas;
// use App\Http\Livewire\Menureserva;
use App\Http\Livewire\SolicitudesReserva;
use App\Http\Livewire\Cambiarpass;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/','login');

Route::redirect('/register','login');


// Route::get('/reserva', function () {
//     return view('livewire.reserva');
// });

// Route::middleware(['auth:sanctum', 'verified'])->get('/reserva')->name('reserva');

// Auth:: routes(['register'=>false, 'reset'=>false]);

// Auth::routes(['reset' => false, 'verify' => false]);

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified'
// ])->group(function () {
//     Route::get('/reserva', function () {
//         return view('layouts.app');
//     })->name('reserva');
// });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->get('/reserva', Reserva::class)->name('reserva');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->get('/listarreservas', Listarreservas::class)->name('listarreservas');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->get('/Cambiarpass', Cambiarpass::class)->name('cambiarpass');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->get('/solicitudesreserva', SolicitudesReserva::class)->name('solicitudesreserva');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/menureserva', function () {
        return view('livewire.menureserva');
    })->name('menureserva');

    // Route::get('/listarreservas', function () {
    //     return view('livewire.listarreservas');
    // })->name('listarreservas');
});
