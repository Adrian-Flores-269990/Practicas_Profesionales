<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\EncargadoController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\DssppController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BitacoraController;

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
    Route::get('/consultar_alumno', fn () => view('administrador.consultar_alumno'))->name('administrador.consultar_alumno');
    Route::get('/empleados', [AdminController::class, 'index'])->name('administrador.empleados');
    Route::post('/empleados', [AdminController::class, 'store'])->name('administrador.empleados.store');
    Route::put('/empleados/{id}', [AdminController::class, 'update'])->name('administrador.empleados.update');
    Route::delete('/empleados/{id}', [AdminController::class, 'destroy'])->name('administrador.empleados.destroy');
    Route::get('/consultar-alumno', [AdminController::class, 'consultarAlumno'])->name('administrador.consultar_alumno');
    Route::put('/empleados/{id}/rol', [AdminController::class, 'actualizarRol'])->name('administrador.actualizarRol');
    Route::get('/alumnos/filtrar', [AdminController::class, 'filtrarAlumnosPorEstado'])->name('administrador.filtrar_alumnos');
    Route::post('/empleados/asignar-rol', [AdminController::class, 'asignarRol'])->name('administrador.empleados.asignarRol');
    Route::post('/actualizar-imagen/{nombre}', [AdminController::class, 'actualizarImagen'])->name('admin.actualizarImagen');
    Route::get('/encargados', [AdminController::class, 'indexEncargados'])->name('administrador.encargados');
    Route::put('/encargados/{id}/carreras', [AdminController::class, 'updateCarreras'])->name('administrador.encargados.updateCarreras');
    Route::get('/empresas', [AdminController::class, 'indexEmpresas'])->name('administrador.empresas');
    Route::get('/constancias', [AdminController::class, 'constancias'])->name('administrador.constancias');
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('admin.bitacora');
});

/*
|--------------------------------------------------------------------------
| RUTAS ALUMNO
|--------------------------------------------------------------------------
*/
Route::prefix('alumno')->group(function () {
    Route::get('/inicio', fn () => view('alumno.inicio'))->name('alumno.inicio');
    Route::get('/estado', [AlumnoController::class, 'estadoAlumno'])->name('alumno.estado');
    Route::get('/solicitud', function () {
        $empresas = \App\Models\DependenciaEmpresa::orderBy('Nombre_Depn_Emp')->get();
        return view('alumno.solicitud', compact('empresas'));
    })->name('alumno.solicitud');
    Route::get('/registro', [AlumnoController::class, 'confirmaFPP02'])->name('alumno.registro');
    Route::put('/confirma', [AlumnoController::class, 'aceptar'])->name('alumno.confirma');
    Route::post('/rechazar', [AlumnoController::class, 'rechazar'])->name('alumno.rechazar');
    Route::post('/generar-fpp02', [PdfController::class, 'generarFpp02'])->name('alumno.generarFpp02');
    Route::post('/fpp02/subir-firmado', [\App\Http\Controllers\PdfController::class, 'subirFpp02Firmado'])->name('alumno.subirFpp02Firmado');
    Route::get('/reporte', fn () => view('alumno.reporte'))->name('alumno.reporte');
    Route::get('/evaluacion', fn () => view('alumno.evaluacion'))->name('alumno.evaluacion');

    // Expediente
    Route::prefix('expediente')->group(function () {
        Route::get('/solicitudFPP01', [SolicitudController::class, 'index'])->name('alumno.expediente.solicitudes');
        Route::get('/solicitudFPP01/{id}', [SolicitudController::class, 'show'])->name('alumno.expediente.verSolicitud');
        Route::get('/solicitudFPP01/{id}/editar', [SolicitudController::class, 'edit'])->name('alumno.expediente.solicitud.editar');
        Route::get('/solicitudFPP01/{id}', [SolicitudController::class, 'show'])->name('solicitud.show');
        Route::post('/solicitudFPP01/store', [SolicitudController::class, 'store'])->name('alumno.expediente.store');

        Route::get('/registroFPP02', fn () => view('alumno.expediente.registroFPP02'))->name('alumno.expediente.registroFPP02');
        Route::get('/reporteFinal', fn () => view('alumno.expediente.reporteFinal'))->name('alumno.expediente.reporteFinal');
        Route::get('/reportesParciales', fn () => view('alumno.expediente.reportesParciales'))->name('alumno.expediente.reportesParciales');

        Route::get('/reciboPago', [\App\Http\Controllers\ReciboController::class, 'vistaReciboPago'])
            ->name('alumno.expediente.reciboPago');
        Route::get('/reciboPago/descargar', [\App\Http\Controllers\ReciboController::class, 'descargarReciboPago'])
            ->name('alumno.expediente.reciboPago.descargar');
        Route::get('/ayudaEconomica', function() {
            $alumno = session('alumno');
            $clave = $alumno['cve_uaslp'] ?? null;
            if (!$clave) return redirect()->route('alumno.inicio');
            $solicitud = \App\Models\SolicitudFPP01::where('Clave_Alumno', $clave)->latest('Id_Solicitud_FPP01')->first();
            $expediente = \App\Models\Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();
            if (is_null($expediente['Carta_Desglose_Percepciones'])) {
                return redirect()->route('desglosePercepciones.mostrar', [
                            'claveAlumno' => $clave,
                            'tipo' => 'Carta_Desglose_Percepciones'
                            ])->with('error', 'Debes subir la Carta de Desglose de Percepciones primero.');
            }
            return view('alumno.expediente.ayudaEconomica');
        })->name('alumno.expediente.ayudaEconomica');
        
        // Upload PDFs
        Route::get('/cartaAceptacion/{claveAlumno}/{tipo}', [PdfController::class, 'mostrarDocumento'])->name('cartaAceptacion.mostrar');
        Route::post('/cartaAceptacion/upload', [PdfController::class, 'subirCartaAceptacion'])->name('cartaAceptacion.upload');
        Route::post('/cartaAceptacion/{claveAlumno}', [PdfController::class, 'eliminarDocumento'])->name('cartaAceptacion.eliminar');

        Route::get('/desglosePercepciones/{claveAlumno}/{tipo}', [PdfController::class, 'mostrarDocumento'])->name('desglosePercepciones.mostrar');
        Route::post('/desglosePercepciones/upload', [PdfController::class, 'subirDesglosePercepciones'])->name('desglosePercepciones.upload');
        Route::post('/desglosePercepciones/{claveAlumno}/{tipo}', [PdfController::class, 'eliminarDocumento'])->defaults('tipo', 'Carta_Aceptacion')->name('desglosePercepciones.eliminar');

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

    Route::get('/solicitudes-alumnos', [EncargadoController::class, 'index'])->name('encargado.solicitudes_alumnos');
    Route::get('/estadisticas_empresas', fn () => view('encargado.estadisticas_empresas'))->name('encargado.estadisticas_empresas');
    Route::get('/consultar-alumno', [EncargadoController::class, 'consultarAlumno'])->name('encargado.consultar_alumno');
    Route::get('/alumnos_en_proceso', [EncargadoController::class, 'alumnosEnProceso'])->name('encargado.alumnos_en_proceso');
    Route::get('/alumnos_finalizados', [EncargadoController::class, 'alumnosFinalizados'])->name('encargado.alumnos_finalizados');

    Route::get('/registrar-empresa', function() {
        return view('encargado.registrar_empresa');
    })->name('encargado.registrar_empresa');

    Route::post('/guardar-empresa', function() {
        return redirect()->route('encargado.inicio')->with('success', 'Empresa registrada exitosamente');
    })->name('encargado.guardar_empresa');

});


/*
|--------------------------------------------------------------------------
| RUTAS SECRETARIA
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| RUTAS DSSPP
|--------------------------------------------------------------------------
*/
Route::prefix('dsspp')->group(function () {
    Route::get('/inicio', fn () => view('dsspp.inicio'))->name('dsspp.inicio');
    Route::get('/consultar-alumno', fn () => view('dsspp.consultar_alumno'))->name('dsspp.consultar_alumno');
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
Route::get('/solicitud/create', [SolicitudController::class, 'create'])->name('solicitud.create');
Route::post('/solicitud', [SolicitudController::class, 'store'])->name('solicitud.store');
