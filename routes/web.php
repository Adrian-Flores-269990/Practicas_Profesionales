<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\EncargadoController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\DssppController;

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
    //Route::get('/estado', fn () => view('alumno.estado'))->name('alumno.estado');
    Route::get('/estado', [AlumnoController::class, 'estadoAlumno'])->name('alumno.estado');
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
        Route::post('/carta-aceptacion/eliminar', [PdfController::class, 'eliminarCartaAceptacion'])->name('alumno.carta-aceptacion.eliminar');
        Route::post('/desglose-percepciones/upload', [PdfController::class, 'subirDesglosePercepciones'])->name('alumno.desglose-percepciones.upload');
        Route::post('/desglose-percepciones/eliminar', [PdfController::class, 'eliminarDesglosePercepciones'])->name('alumno.desglose-percepciones.eliminar');

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

    //Route::get('/alumnos_en_proceso', fn () => view('encargado.alumnos_en_proceso'))->name('encargado.alumnos_en_proceso');

    Route::get('/solicitudes-alumnos', [EncargadoController::class, 'index'])
        ->name('encargado.solicitudes_alumnos');

    Route::get('/estadisticas_empresas', fn () => view('encargado.estadisticas_empresas'))->name('encargado.estadisticas_empresas');

    Route::get('/consultar-alumno', [EncargadoController::class, 'consultarAlumno'])
    ->name('encargado.consultar_alumno');

    Route::get('/alumnos_en_proceso', [EncargadoController::class, 'alumnosEnProceso'])
        ->name('encargado.alumnos_en_proceso');

    Route::get('/alumnos_finalizados', [EncargadoController::class, 'alumnosFinalizados'])
        ->name('encargado.alumnos_finalizados');

    Route::get('/registrar-empresa', function() {
        return view('encargado.registrar_empresa');
    })->name('encargado.registrar_empresa');

    // Ruta para guardar (puedes implementarla después)
    Route::post('/guardar-empresa', function() {
        // TODO: Implementar guardado en base de datos

        // Por ahora solo simular
        return redirect()->route('encargado.inicio')->with('success', '✅ Empresa registrada exitosamente');
    })->name('encargado.guardar_empresa');

});

// SECRETARIA
Route::prefix('secretaria')->group(function () {
    Route::get('/inicio', fn () => view('secretaria.inicio'))->name('secretaria.inicio');

    // Generar Constancias
    Route::get('/generar-constancias', function() {
        $alumnos = [
            [
                'clave' => '194659',
                'nombre' => 'Juan Carlos García López',
                'correo' => 'juan.garcia@alumnos.uaslp.edu.mx',
                'carrera' => 'Ing. en Software',
                'fecha_termino' => '2024-12-15',
                'constancia_generada' => false
            ],
            [
                'clave' => '195432',
                'nombre' => 'María Fernanda Martínez Sánchez',
                'correo' => 'maria.martinez@alumnos.uaslp.edu.mx',
                'carrera' => 'Ing. Civil',
                'fecha_termino' => '2024-11-20',
                'constancia_generada' => true
            ],
            [
                'clave' => '196543',
                'nombre' => 'Pedro Alberto Ramírez Torres',
                'correo' => 'pedro.ramirez@alumnos.uaslp.edu.mx',
                'carrera' => 'Ing. Industrial',
                'fecha_termino' => '2024-10-30',
                'constancia_generada' => false
            ],
        ];

        return view('secretaria.generar_constancia', compact('alumnos'));
    })->name('secretaria.generar_constancia');



    // Consultar Constancias
    Route::get('/consultar-constancias', function() {
        $constancias = [
            [
                'folio' => 'CONST-2024-001',
                'clave' => '195432',
                'nombre' => 'María Fernanda Martínez Sánchez',
                'correo' => 'maria.martinez@alumnos.uaslp.edu.mx',
                'carrera' => 'Ing. Civil',
                'fecha_generacion' => '2024-11-25'
            ],
            [
                'folio' => 'CONST-2024-002',
                'clave' => '197654',
                'nombre' => 'Ana Sofía Hernández Cruz',
                'correo' => 'ana.hernandez@alumnos.uaslp.edu.mx',
                'carrera' => 'Ing. Mecánica',
                'fecha_generacion' => '2024-12-01'
            ],
            [
                'folio' => 'CONST-2024-003',
                'clave' => '198765',
                'nombre' => 'Carlos Eduardo Ruiz Flores',
                'correo' => 'carlos.ruiz@alumnos.uaslp.edu.mx',
                'carrera' => 'Ing. en Software',
                'fecha_generacion' => '2024-12-10'
            ],
        ];

        return view('secretaria.validar_constancia', compact('constancias'));
    })->name('secretaria.validar_constancia');
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
    //  Inicio
    Route::get('/inicio', fn () => view('dsspp.inicio'))->name('dsspp.inicio');

    //  Consultar alumno (vista de búsqueda)
    Route::get('/consultar-alumno', fn () => view('dsspp.consultar_alumno'))->name('dsspp.consultar_alumno');

    //  Solicitudes pendientes
    Route::get('/solicitudes', [DssppController::class, 'index'])->name('dsspp.solicitudes');

    Route::get('/solicitud/{id}', [DssppController::class, 'verSolicitud'])->name('dsspp.verSolicitud');
    Route::put('/solicitud/{id}/autorizar', [DssppController::class, 'autorizarSolicitud'])->name('dsspp.autorizarSolicitud');

});


/*
|--------------------------------------------------------------------------
| RUTAS PDFs / RECIBOS
|--------------------------------------------------------------------------
*/
Route::post('/recibo/descargar', [ReciboController::class, 'descargar'])->name('recibo.descargar');
