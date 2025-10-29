<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SolicitudController;

use App\Http\Controllers\AlumnoController;

Route::get('/alumno/crear', [AlumnoController::class, 'create'])->name('alumno.create');
Route::post('/alumno/guardar', [AlumnoController::class, 'store'])->name('alumno.store');

// SOLICITUD
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

    
    Route::get('/expediente/solicitudFPP01', [SolicitudController::class, 'create'])->name('alumno.expediente.solicitudFPP01');
    Route::post('/solicitud/store', [SolicitudController::class, 'store'])->name('solicitud.store');


    Route::get('/expediente/registroFPP02', fn () => view('alumno.expediente.registroFPP02'))->name('alumno.expediente.registroFPP02');
    Route::get('/expediente/reporteFinal', fn () => view('alumno.expediente.reporteFinal'))->name('alumno.expediente.reporteFinal');
    Route::get('/expediente/reportesParciales', fn () => view('alumno.expediente.reportesParciales'))->name('alumno.expediente.reportesParciales');


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
    
    Route::get('/consultar-alumno', function() {
        $alumnos = [
            [
                'nombres' => 'Juan Carlos',
                'paterno' => 'García',
                'materno' => 'López',
                'cve_uaslp' => '194659',
                'carrera' => 'Ingeniería en informatica',
                'semestre' => '7',
                'creditos' => '280',
                'correo' => 'juan.garcia@alumnos.uaslp.edu.mx'
            ],
            [
                'nombres' => 'María Fernanda',
                'paterno' => 'Martínez',
                'materno' => 'Sánchez',
                'cve_uaslp' => '195432',
                'carrera' => 'Ingeniería Civil',
                'semestre' => '8',
                'creditos' => '320',
                'correo' => 'maria.martinez@alumnos.uaslp.edu.mx'
            ],
        ];
        return view('encargado.consultar_alumno', compact('alumnos'));
    })->name('encargado.consultar_alumno'); 
    


Route::get('/solicitudes-alumnos', function() {
    // Datos estáticos de ejemplo
    $solicitudes = [
        [
            'id' => 1,
            'alumno_nombre' => 'Juan Carlos García López',
            'alumno_clave' => '194659',
            'carrera' => 'Ing. en Software',
            'fecha_solicitud' => '2024-10-15',
            'empresa' => 'Tecnologías SA',
            'fecha_inicio' => '2025-01-15',
            'fecha_termino' => '2025-06-15',
            'tipo' => 'Sector Privado',
            'estado' => 'pendiente'
        ],
        [
            'id' => 2,
            'alumno_nombre' => 'María Fernanda Martínez',
            'alumno_clave' => '195432',
            'carrera' => 'Ing. Civil',
            'fecha_solicitud' => '2024-10-20',
            'empresa' => 'Construcciones MX',
            'fecha_inicio' => '2025-02-01',
            'fecha_termino' => '2025-07-01',
            'tipo' => 'Sector Público',
            'estado' => 'revision'
        ],
    ];
    
    $registros = [
        [
            'id' => 3,
            'alumno_nombre' => 'Pedro Alberto Ramírez',
            'alumno_clave' => '196543',
            'fecha_aprobacion_solicitud' => '2024-09-15',
            'fecha_registro' => '2024-10-01',
            'empresa' => 'Industrias del Norte',
            'asesor_interno' => 'Dr. José Luis Pérez'
        ],
    ];
    
    $rechazadas = [
        [
            'id' => 4,
            'alumno_nombre' => 'Ana Sofía Hernández',
            'alumno_clave' => '197654',
            'carrera' => 'Ing. Mecánica',
            'fecha_rechazo' => '2024-10-10',
            'comentario_rechazo' => 'Falta documentación de la empresa. Por favor adjunta el convenio firmado y la carta de aceptación.'
        ],
    ];
    
    $stats = [
        'pendientes' => count($solicitudes),
        'aprobadas' => count($registros),
        'rechazadas' => count($rechazadas)
    ];
    
    return view('encargado.solicitudes_alumnos', compact('solicitudes', 'registros', 'rechazadas', 'stats'));
})->name('encargado.solicitudes_alumnos');
    

    Route::get('/estadisticas_empresas', fn () => view('encargado.estadisticas_empresas'))->name('encargado.estadisticas_empresas');

    Route::get('/alumnos_en_proceso', function() {
    $alumnos = [
        [
            'nombres' => 'Juan Carlos',
            'paterno' => 'García',
            'materno' => 'López',
            'cve_uaslp' => '194659',
            'carrera' => 'Ing. en Software',
            'materia' => 'PP-420',
            'area' => 'Desarrollo Web',
            'progreso' => 85,
            'estado' => 'excelente',
            'pasos' => [
                'solicitud' => 'completed',
                'registro' => 'completed',
                'reportes' => 'in-progress',
                'evaluacion' => 'pending'
            ]
        ],
        [
            'nombres' => 'María Fernanda',
            'paterno' => 'Martínez',
            'materno' => 'Sánchez',
            'cve_uaslp' => '195432',
            'carrera' => 'Ing. Civil',
            'materia' => 'PP-421',
            'area' => 'Construcción',
            'progreso' => 45,
            'estado' => 'regular',
            'pasos' => [
                'solicitud' => 'completed',
                'registro' => 'in-progress',
                'reportes' => 'pending',
                'evaluacion' => 'pending'
            ]
        ],
        [
            'nombres' => 'Pedro Alberto',
            'paterno' => 'Ramírez',
            'materno' => 'Torres',
            'cve_uaslp' => '196543',
            'carrera' => 'Ing. Industrial',
            'materia' => 'PP-422',
            'area' => 'Producción',
            'progreso' => 25,
            'estado' => 'atrasado',
            'pasos' => [
                'solicitud' => 'completed',
                'registro' => 'pending',
                'reportes' => 'pending',
                'evaluacion' => 'pending'
            ]
        ],
    ];
    return view('encargado.alumnos_en_proceso', compact('alumnos'));
    })->name('encargado.alumnos_en_proceso');






    Route::get('/alumnos_terminados', function() {
        $alumnos = [
            [
                'clave' => '194659',
                'nombre' => 'Juan Carlos García López',
                'correo' => 'juan.garcia@alumnos.uaslp.edu.mx',
                'carrera' => 'Ing. en Software',
                'materia' => 'PP-420',
                'solicitud' => 'Aprobada',  // Esta clave debe existir
                'estado' => 'Completado'
            ],
            [
                'clave' => '195432',
                'nombre' => 'María Fernanda Martínez Sánchez',
                'correo' => 'maria.martinez@alumnos.uaslp.edu.mx',
                'carrera' => 'Ing. Civil',
                'materia' => 'PP-421',
                'solicitud' => 'Aprobada',  // Esta clave debe existir
                'estado' => 'Aprobado'
            ],
            [
                'clave' => '196543',
                'nombre' => 'Pedro Alberto Ramírez Torres',
                'correo' => 'pedro.ramirez@alumnos.uaslp.edu.mx',
                'carrera' => 'Ing. Industrial',
                'materia' => 'PP-422',
                'solicitud' => 'En Revisión',  // Esta clave debe existir
                'estado' => 'Finalizado'
            ],
        ];
        
        return view('encargado.alumnos_finalizados', compact('alumnos'));
    })->name('encargado.alumnos_finalizados');


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
