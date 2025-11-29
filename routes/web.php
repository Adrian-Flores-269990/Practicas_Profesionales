<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\EncargadoController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\DssppController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ReporteController;
use App\Models\SolicitudFPP01;
use App\Models\Expediente;
use App\Http\Controllers\ModalController;

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

Route::put('/modals/{modal}', [ModalController::class, 'update'])->name('modals.update');

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
    Route::delete('/empresas/{id}', [AdminController::class, 'destroyEmpresa'])->name('administrador.empresa.destroy');
    Route::get('/constancias', [AdminController::class, 'constancias'])->name('administrador.constancias');
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('admin.bitacora');
    Route::get('/autorizaciones', [AdminController::class, 'autorizacionesPendientes'])->name('administrador.autorizaciones');
    Route::get('/estadisticas-empresas', [EstadisticaController::class, 'index'])->name('estadisticas-empresas.index');
    Route::get('/estadisticas-empresas/get-datos', [EstadisticaController::class, 'getDatos'])->name('estadisticas-empresas.getDatos');
});

/*
|--------------------------------------------------------------------------
| RUTAS ALUMNO
|--------------------------------------------------------------------------
*/
Route::prefix('alumno')->group(function () {
    Route::get('/inicio', fn () => view('alumno.inicio'))->name('alumno.inicio');
    Route::get('/estado', [AlumnoController::class, 'estadoAlumno'])->name('alumno.estado');
    Route::get('/guardar-materia', [AlumnoController::class, 'guardarMateria'])->name('alumno.guardarMateria');
    Route::get('/solicitud', function (Request $request) {
        $materia = $request->materia;
        if (!$materia) {
            $materia = session('materia_practicas');
        }
        if ($materia) {
            session(['materia_practicas' => $materia]);
        }

        $empresas = \App\Models\DependenciaEmpresa::orderBy('Nombre_Depn_Emp')->get();
        return view('alumno.solicitud', compact('empresas', 'materia'));
    })->name('alumno.solicitud');

    Route::get('/registro', [AlumnoController::class, 'confirmaFPP02'])->name('alumno.registro');
    Route::put('/confirma', [AlumnoController::class, 'aceptar'])->name('alumno.confirma');
    Route::post('/rechazar', [AlumnoController::class, 'rechazar'])->name('alumno.rechazar');
    Route::post('/generar-fpp02', [PdfController::class, 'generarFpp02'])->name('alumno.generarFpp02');

    // Reportes
    Route::get('/reporte', [\App\Http\Controllers\ReporteController::class, 'create'])->name('alumno.reporte');
    Route::post('/reporte', [\App\Http\Controllers\ReporteController::class, 'store'])->name('alumno.reportes.store');
    Route::get('/reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('alumno.reportes.lista');
    Route::get('/reportes/{id}/descargar', [\App\Http\Controllers\ReporteController::class, 'descargar'])->name('alumno.reportes.descargar');

    Route::get('/evaluacion', fn () => view('alumno.evaluacion'))->name('alumno.evaluacion');

    Route::get('/registroFPP02/{claveAlumno}/{tipo}', [PdfController::class, 'mostrarDocumento'])->name('registroFPP02.mostrar');
    Route::post('/registroFPP02/upload', [PdfController::class, 'subirFpp02Firmado'])->name('registroFPP02.upload');
    Route::post('/registroFPP02/{claveAlumno}/{tipo}', [PdfController::class, 'eliminarDocumento'])->defaults('tipo', 'Solicitud_FPP02_Firmada')->name('registroFPP02.eliminar');

    // Expediente
    Route::prefix('expediente')->group(function () {
        Route::get('/solicitudFPP01', [SolicitudController::class, 'index'])->name('alumno.expediente.solicitudes');
        Route::get('/solicitudFPP01/{id}', [SolicitudController::class, 'show'])->name('alumno.expediente.verSolicitud');
        Route::get('/solicitudFPP01/{id}/editar', [SolicitudController::class, 'edit'])->name('alumno.expediente.solicitud.editar');
        Route::get('/solicitudFPP01/{id}', [SolicitudController::class, 'show'])->name('solicitud.show');
        Route::post('/solicitudFPP01/store', [SolicitudController::class, 'store'])->name('alumno.expediente.store');

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
            $solicitud = SolicitudFPP01::where('Clave_Alumno', $clave)->latest('Id_Solicitud_FPP01')->first();
            $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();
            if (is_null($expediente['Carta_Desglose_Percepciones'])) {
                return redirect()->route('desglosePercepciones.mostrar', [
                            'claveAlumno' => $clave,
                            'tipo' => 'Carta_Desglose_Percepciones'
                            ])->with('error', 'Debes subir la Carta de Desglose de Percepciones primero.');
            }
            return view('alumno.expediente.ayudaEconomica');
        })->name('alumno.expediente.ayudaEconomica');

        // Carta Presentación
        Route::get('/cartaPresentacion/{claveAlumno}/{tipo}', [PdfController::class, 'mostrarDocumento'])->name('cartaPresentacion.mostrar');
        Route::post('/cartaPresentacion/upload', [PdfController::class, 'subirCartaPresentacion'])->name('cartaPresentacion.upload');
        Route::post('/cartaPresentacion/{claveAlumno}/{tipo}', [PdfController::class, 'eliminarDocumento'])->defaults('tipo', 'Carta_Aceptacion')->name('cartaPresentacion.eliminar');

        // Upload PDFs
        Route::get('/cartaAceptacion/{claveAlumno}/{tipo}', [PdfController::class, 'mostrarDocumento'])->name('cartaAceptacion.mostrar');
        Route::post('/cartaAceptacion/upload', [PdfController::class, 'subirCartaAceptacion'])->name('cartaAceptacion.upload');
        Route::post('/cartaAceptacion/{claveAlumno}/{tipo}', [PdfController::class, 'eliminarDocumento'])->name('cartaAceptacion.eliminar');

        Route::get('/desglosePercepciones/{claveAlumno}/{tipo}', [PdfController::class, 'mostrarDocumento'])->name('desglosePercepciones.mostrar');
        Route::post('/desglosePercepciones/upload', [PdfController::class, 'subirDesglosePercepciones'])->name('desglosePercepciones.upload');
        Route::post('/desglosePercepciones/{claveAlumno}/{tipo}', [PdfController::class, 'eliminarDocumento'])->name('desglosePercepciones.eliminar');
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

    // Solicitudes
    Route::get('/solicitudes', [EncargadoController::class, 'index'])->name('encargado.solicitudes');
    Route::get('/solicitudes-alumnos', [EncargadoController::class, 'index'])->name('encargado.solicitudes_alumnos'); // Mismo método index
    Route::get('/solicitud/{id}', [EncargadoController::class, 'verSolicitud'])->name('encargado.verSolicitud');
    Route::put('/solicitud/{id}/autorizar', [EncargadoController::class, 'autorizarSolicitud'])->name('encargado.autorizar');

    Route::get('/solicitudes-alumnos', [EncargadoController::class, 'index'])->name('encargado.solicitudes_alumnos');
    Route::get('/estadisticas-empresas', fn () => view('encargado.estadisticas-empresas'))->name('encargado.estadisticas_empresas');
    Route::get('/consultar-alumno', [EncargadoController::class, 'consultarAlumno'])
        ->name('encargado.consultar_alumno');

    Route::get('/alumnos_en_proceso', [EncargadoController::class, 'alumnosEnProceso'])
        ->name('encargado.alumnos_en_proceso');

    Route::get('/alumnos_finalizados', [EncargadoController::class, 'alumnosFinalizados'])
        ->name('encargado.alumnos_finalizados');

    Route::get('/registrar-empresa', function() {
        return view('encargado.registrar_empresa');
    })->name('encargado.registrar_empresa');

    Route::post('/guardar-empresa', function() {
        return redirect()->route('encargado.inicio')->with('success', 'Empresa registrada exitosamente');
    })->name('encargado.guardar_empresa');

    //Registros
    Route::get('/registros', [EncargadoController::class, 'verRegistros'])->name('encargado.registros');
    Route::get('/registro/{claveAlumno}/{tipo}/{documento}', [PdfController::class, 'mostrarDocumentoEmpleados'])->name('encargado.verRegistro');
    Route::post('/accion_registro', [EncargadoController::class, 'calificarRegistro'])->name('encargado.calificarRegistro');

    //Carta de Presentación
    Route::get('/cartas_presentacion', [EncargadoController::class, 'cartasPresentacionEncargado'])->name('encargado.cartasPresentacion');
    //Route::get('/carta_presentacion/{claveAlumno}/{tipo}/{documento}', [PdfController::class, 'mostrarDocumentoEmpleados'])->name('encargado.verCartaPresentacion');
    Route::post('/accion_carta_presentacion', [EncargadoController::class, 'calificarPresentacion'])->name('encargado.calificarCartaPresentacion');
    Route::get('/cartas_presentacion/{claveAlumno}', [EncargadoController::class, 'revisarCartaPresentacion'])->name('encargado.verCartaPresentacion');
    Route::post('/carta/accion',[EncargadoController::class, 'accionCartaPresentacion'])->name('encargado.cartaPresentacion.accion');
    //Route::post('/cartas/calificar', [EncargadoController::class, 'calificarPresentacion'])->name('encargado.calificarCartaPresentacion');



    //Carta de Aceptación
    Route::get('/cartas_aceptacion', [EncargadoController::class, 'verAceptacion'])->name('encargado.cartasAceptacion');
    Route::get('/carta_aceptacion/{claveAlumno}/{tipo}/{documento}', [PdfController::class, 'mostrarDocumentoEmpleados'])->name('encargado.verCartaAceptacion');
    Route::post('/accion_carta_aceptacion', [EncargadoController::class, 'calificarAceptacion'])->name('encargado.calificarCartaAceptacion');

    // Reportes
    Route::get('/reportes/pendientes', [ReporteController::class, 'reportesPendientes'])->name('encargado.reportes.pendientes');
    Route::get('/reportes/alumno/{clave}', [ReporteController::class, 'reportesAlumno'])->name('encargado.reportes_alumno');
    Route::get('/reportes/{id}/revisar', [ReporteController::class, 'revisar'])->name('encargado.reportes.revisar');
    Route::post('/reportes/{id}/aprobar', [ReporteController::class, 'aprobar'])->name('encargado.reportes.aprobar');
    Route::post('/reportes/{id}/aprobar', [ReporteController::class, 'calificarFinal'])->name('encargado.reportes.calificarFinal');
    Route::get('/reportes/{id}/descargar', [ReporteController::class, 'descargarEncargado'])->name('encargado.reportes.descargar');

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

    Route::get('/carta', [DssppController::class, 'lista'])->name('dsspp.carta');
    Route::post('/carta/generar/{clave}', [DssppController::class, 'generarCarta'])->name('dsspp.carta.generar');
    Route::post('/carta/aprobar/{clave}', [DssppController::class, 'aprobar'])->name('dsspp.carta.aprobar');
    Route::post('/carta/rechazar/{clave}', [DssppController::class, 'rechazar'])->name('dsspp.carta.rechazar');
    Route::get('/carta/preview/{clave}', [DssppController::class, 'previewCarta'])->name('dsspp.carta.preview');
});


/*
|--------------------------------------------------------------------------
| RUTAS PDFs / RECIBOS
|--------------------------------------------------------------------------
*/
Route::post('/recibo/descargar', [ReciboController::class, 'descargar'])->name('recibo.descargar');
Route::get('/solicitud/create', [SolicitudController::class, 'create'])->name('solicitud.create');
Route::post('/solicitud', [SolicitudController::class, 'store'])->name('solicitud.store');
