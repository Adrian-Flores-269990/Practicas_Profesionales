<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// HOME (raíz)
Route::view('/', 'welcome')->name('welcome');

// ADMIN HOME
Route::get('/admin/home', fn () => view('administrador.home'))->name('admin.home');

// ALUMNO
Route::prefix('alumno')->group(function () {
    Route::get('/home', fn () => view('alumno.inicio'))->name('alumno.home');
    Route::get('/estado', fn () => view('alumno.estado'))->name('alumno.estado');

    Route::get('/solicitud', fn () => view('alumno.solicitud'))->name('alumno.solicitud');
    Route::get('/registro', fn () => view('alumno.registro'))->name('alumno.registro');
    
    Route::get('/reporte', fn () => view('alumno.reporte'))->name('alumno.reporte');
    Route::get('/evaluacion', fn () => view('alumno.evaluacion'))->name('alumno.evaluacion');
    Route::get('/expediente/cartaAceptacion', fn () => view('alumno.expediente.cartaAceptacion'))->name('alumno.expediente.cartaAceptacion');
    Route::get('/expediente/desglosePercepciones', fn () => view('alumno.expediente.desglosePercepciones'))->name('alumno.expediente.desglosePercepciones');
    Route::get('/expediente/reciboPago', fn () => view('alumno.expediente.reciboPago'))->name('alumno.expediente.reciboPago');
});

// SECRETARIA HOME
Route::get('/secretaria/home', fn () => view('secretaria.home'))->name('secretaria.home');

// SECRETARIA HOME
Route::get('/encargado/home', fn () => view('encargado.home'))->name('encargado.home');

// SECRETARIA HOME
Route::get('/dsspp/home', fn () => view('dsspp.home'))->name('dsspp.home');

// LOGINS
Route::view('/alumno/login', 'auth.login')->name('alumno.login');
Route::view('/empleado/login', 'auth.loginEmpleado')->name('empleado.login');

// POST login
Route::post('/', [AuthController::class, 'login'])->name('login');
