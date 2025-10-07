<?php

use Illuminate\Support\Facades\Route;
<<<<<<< Updated upstream
use App\Http\Controllers\AuthController;
=======
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SolicitudController;

Route::post('/solicitud/store', [SolicitudController::class, 'store'])->name('solicitud.store');
//Route::post('/solicitud', [SolicitudController::class, 'store'])->name('solicitud.store');

>>>>>>> Stashed changes

// HOME (raíz)
Route::view('/', 'welcome')->name('home');

// ADMIN HOME
Route::get('/admin/home', fn () => view('administrador.home'))->name('admin.home');

// ALUMNO
Route::prefix('alumno')->group(function () {
    Route::get('/home', fn () => view('alumno.inicio'))->name('alumno.home');
    Route::get('/estado', fn () => view('alumno.estado'))->name('alumno.estado');
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
Route::get('/encargado/solicitudes_alumnos', fn () => view('encargado.solicitudes_alumnos'))->name('encargado.solicitudes_alumnos');
Route::get('/encargado/alumnos_en_proceso', fn () => view('encargado.alumnos_en_proceso'))->name('encargado.alumnos_en_proceso');
Route::get('/encargado/estadisticas_empresas', fn () => view('encargado.estadisticas_empresas'))->name('encargado.estadisticas_empresas');

// SECRETARIA HOME
Route::get('/dsspp/home', fn () => view('dsspp.home'))->name('dsspp.home');

// LOGINS
Route::view('/alumno/login', 'auth.login')->name('alumno.login');
Route::view('/empleado/login', 'auth.loginEmpleado')->name('empleado.login');

// POST login
Route::post('/', [AuthController::class, 'login'])->name('login');
