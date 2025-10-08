<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SolicitudController;

use App\Http\Controllers\AlumnoController;

Route::get('/alumno/crear', [AlumnoController::class, 'create'])->name('alumno.create');
Route::post('/alumno/guardar', [AlumnoController::class, 'store'])->name('alumno.store');

Route::post('/solicitud/store', [SolicitudController::class, 'store'])->name('solicitud.store');

// HOME (raíz)
Route::view('/', 'welcome')->name('welcome');

// ADMIN
Route::prefix('admin')->group(function () {
    Route::get('/inicio', fn () => view('administrador.inicio'))->name('administrador.inicio');
});

// ALUMNO
Route::prefix('alumno')->group(function () {
    Route::get('/inicio', fn () => view('alumno.inicio'))->name('alumno.inicio');

    Route::get('/estado', fn () => view('alumno.estado'))->name('alumno.estado');
    Route::get('/solicitud', fn () => view('alumno.solicitud'))->name('alumno.solicitud');
    Route::get('/registro', fn () => view('alumno.registro'))->name('alumno.registro');
    Route::get('/reporte', fn () => view('alumno.reporte'))->name('alumno.reporte');
    Route::get('/evaluacion', fn () => view('alumno.evaluacion'))->name('alumno.evaluacion');

    Route::get('/expediente/cartaAceptacion', fn () => view('alumno.expediente.cartaAceptacion'))->name('alumno.expediente.cartaAceptacion');
    Route::get('/expediente/desglosePercepciones', fn () => view('alumno.expediente.desglosePercepciones'))->name('alumno.expediente.desglosePercepciones');
    Route::get('/expediente/reciboPago', fn () => view('alumno.expediente.reciboPago'))->name('alumno.expediente.reciboPago');
    Route::get('/expediente/ayudaEconomica', fn () => view('alumno.expediente.ayudaEconomica'))->name('alumno.expediente.ayudaEconomica');

    Route::view('/faq',  'alumno.faq')->name('dev.alumno.faq');
    Route::view('/detalles',  'alumno.detalles')->name('dev.alumno.detalles');
    Route::view('/diagrama',  'alumno.diagrama')->name('dev.alumno.diagrama');
    Route::view('/proceso',  'alumno.proceso')->name('dev.alumno.proceso');
});

// SECRETARIA
Route::prefix('secretaria')->group(function () {
    Route::get('/inicio', fn () => view('secretaria.inicio'))->name('secretaria.inicio');
});

// ENCARGADO
Route::prefix('encargado')->group(function () {
    Route::get('/inicio', fn () => view('encargado.inicio'))->name('encargado.inicio');
    Route::get('/consultar_alumno', fn () => view('encargado.consultar_alumno'))->name('encargado.consultar_alumno');
    Route::get('/solicitudes_alumnos', fn () => view('encargado.solicitudes_alumnos'))->name('encargado.solicitudes_alumnos');
    Route::get('/alumnos_en_proceso', fn () => view('encargado.alumnos_en_proceso'))->name('encargado.alumnos_en_proceso');
    Route::get('/estadisticas_empresas', fn () => view('encargado.estadisticas_empresas'))->name('encargado.estadisticas_empresas');
});

// DSSPP
Route::prefix('dsspp')->group(function () {
    Route::get('/inicio', fn () => view('dsspp.inicio'))->name('dsspp.inicio');
});


// LOGINS
Route::view('/alumno/login', 'auth.login')->name('alumno.login');
Route::view('/empleado/login', 'auth.loginEmpleado')->name('empleado.login');

// PDFs
Route::post('/recibo/descargar', [ReciboController::class, 'descargar'])->name('recibo.descargar');

// POST login
Route::post('/', [LoginController::class, 'login'])->name('login');

// POST login Empleado
Route::post('/empleado/login', [LoginController::class, 'loginEmpleado'])->name('empleado.login.post');
