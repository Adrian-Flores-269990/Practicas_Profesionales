<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\EncargadoController;
use App\Http\Controllers\AlumnoController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS / LOGIN
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('welcome');

// Login alumno
Route::view('/alumno/login', 'auth.login')->name('alumno.login');
Route::post('/', [LoginController::class, 'login'])->name('login');

// Login empleado/encargado
Route::view('/empleado/login', 'auth.loginEmpleado')->name('empleado.login');
Route::post('/empleado/login', [LoginController::class, 'loginEmpleado'])->name('empleado.login.post');

/*
|--------------------------------------------------------------------------
| RUTAS ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::get('/inicio', fn () => view('administrador.inicio'))->name('administrador.inicio');
});

/*
|--------------------------------------------------------------------------
| RUTAS ALUMNO
|--------------------------------------------------------------------------
*/
Route::prefix('alumno')->group(function () {

    Route::get('/inicio', fn () => view('alumno.inicio'))->name('alumno.inicio');
    Route::get('/estado', fn () => view('alumno.estado'))->name('alumno.estado');
    Route::get('/solicitud', fn () => view('alumno.solicitud'))->name('alumno.solicitud');
    Route::get('/registro', fn () => view('alumno.registro'))->name('alumno.registro');
    Route::get('/reporte', fn () => view('alumno.reporte'))->name('alumno.reporte');
    Route::get('/evaluacion', fn () => view('alumno.evaluacion'))->name('alumno.evaluacion');

    // Expediente
    Route::prefix('expediente')->group(function () {
        Route::get('/solicitudFPP01', [SolicitudController::class, 'index'])->name('alumno.expediente.solicitudes');
        Route::get('/solicitudFPP01/{id}', [SolicitudController::class, 'show'])->name('alumno.expediente.verSolicitud');
        Route::get('/solicitudFPP01/{id}/editar', [SolicitudController::class, 'edit'])->name('alumno.expediente.solicitud.editar');
        Route::post('/solicitudFPP01/store', [SolicitudController::class, 'store'])->name('alumno.expediente.store');

        Route::get('/registroFPP02', fn () => view('alumno.expediente.registroFPP02'))->name('alumno.expediente.registroFPP02');
        Route::get('/reporteFinal', fn () => view('alumno.expediente.reporteFinal'))->name('alumno.expediente.reporteFinal');
        Route::get('/reportesParciales', fn () => view('alumno.expediente.reportesParciales'))->name('alumno.expediente.reportesParciales');

        Route::get('/cartaAceptacion', fn () => view('alumno.expediente.cartaAceptacion'))->name('alumno.expediente.cartaAceptacion');
        Route::get('/desglosePercepciones', fn () => view('alumno.expediente.desglosePercepciones'))->name('alumno.expediente.desglosePercepciones');
        Route::get('/reciboPago', fn () => view('alumno.expediente.reciboPago'))->name('alumno.expediente.reciboPago');
        Route::get('/ayudaEconomica', fn () => view('alumno.expediente.ayudaEconomica'))->name('alumno.expediente.ayudaEconomica');

    // Upload PDFs
    Route::post('/carta-aceptacion/upload', [PdfController::class, 'subirCartaAceptacion'])->name('alumno.carta-aceptacion.upload');
    Route::post('/desglose-percepciones/upload', [PdfController::class, 'subirDesglosePercepciones'])->name('alumno.desglose-percepciones.upload');
    });

    // FAQs y documentación
    Route::view('/faq',  'alumno.faq')->name('dev.alumno.faq');
    Route::view('/detalles',  'alumno.detalles')->name('dev.alumno.detalles');
    Route::view('/diagrama',  'alumno.diagrama')->name('dev.alumno.diagrama');
    Route::view('/proceso',  'alumno.proceso')->name('dev.alumno.proceso');

    // Crear/guardar alumno
    Route::get('/crear', [AlumnoController::class, 'create'])->name('alumno.create');
    Route::post('/guardar', [AlumnoController::class, 'store'])->name('alumno.store');
});

/*
|--------------------------------------------------------------------------
| RUTAS ENCARGADO
|--------------------------------------------------------------------------
*/
Route::prefix('encargado')->group(function () {
    Route::get('/inicio', fn () => view('encargado.inicio'))->name('encargado.inicio');
    Route::get('/consultar_alumno', fn () => view('encargado.consultar_alumno'))->name('encargado.consultar_alumno');

    // Solicitudes
    Route::get('/solicitudes', [EncargadoController::class, 'index'])->name('encargado.solicitudes');
    Route::get('/solicitudes-alumnos', [EncargadoController::class, 'index'])->name('encargado.solicitudes_alumnos'); // Mismo método index
    Route::get('/solicitud/{id}', [EncargadoController::class, 'verSolicitud'])->name('encargado.verSolicitud');
    Route::put('/solicitud/{id}/autorizar', [EncargadoController::class, 'autorizarSolicitud'])->name('encargado.autorizar');

    Route::get('/alumnos_en_proceso', fn () => view('encargado.alumnos_en_proceso'))->name('encargado.alumnos_en_proceso');
    Route::get('/estadisticas_empresas', fn () => view('encargado.estadisticas_empresas'))->name('encargado.estadisticas_empresas');
});
Route::get('/alumno/expediente/solicitudFPP01/{id}', [SolicitudController::class, 'show'])
        ->name('solicitud.show');
Route::get('/solicitud/create', [SolicitudController::class, 'create'])->name('solicitud.create');
    Route::post('/solicitud', [SolicitudController::class, 'store'])->name('solicitud.store');
/*
|--------------------------------------------------------------------------
| RUTAS SECRETARIA
|--------------------------------------------------------------------------
*/
Route::prefix('secretaria')->group(function () {
    Route::get('/inicio', fn () => view('secretaria.inicio'))->name('secretaria.inicio');
});

/*
|--------------------------------------------------------------------------
| RUTAS DSSPP
|--------------------------------------------------------------------------
*/
Route::prefix('dsspp')->group(function () {
    Route::get('/inicio', fn () => view('dsspp.inicio'))->name('dsspp.inicio');
});

/*
|--------------------------------------------------------------------------
| RUTAS PDFs / RECIBOS
|--------------------------------------------------------------------------
*/
Route::post('/recibo/descargar', [ReciboController::class, 'descargar'])->name('recibo.descargar');
