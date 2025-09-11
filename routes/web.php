<?php

// DUDAS EN TODO ESTO, VIEW/GET


use Illuminate\Support\Facades\Route;

// Login (página)
Route::view('/', 'auth.login')->name('login');

// Admin / Alumno
Route::view('/admin/home', 'admin.dashboard')->name('admin.home');
Route::view('/alumno/home', 'alumno.inicio')->name('alumno.home');

// Verificar credenciales
use App\Http\Controllers\AuthController;
Route::post('/login', [AuthController::class, 'login'])->name('login.post');



// DUDAS AQUI
if (app()->environment('local')) {
    Route::prefix('_dev')->group(function () {
        Route::view('/login',  'auth.login')->name('dev.login');
        Route::view('/admin',  'admin.dashboard')->name('dev.admin');
        Route::view('/alumno', 'alumno.inicio')->name('dev.alumno');
    });
}
