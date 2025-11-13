<?php

namespace App\Http\Controllers;

use App\Models\SolicitudFPP01;
use App\Models\DependenciaEmpresa;
use App\Models\DependenciaMercadoSolicitud;
use App\Models\SectorPrivado;
use App\Models\SectorPublico;
use App\Models\SectorUaslp;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\EstadoProceso;
use App\Models\CarreraIngenieria;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::transaction(function() use ($request) {
                $alumno = session('alumno');
                $claveAlumno = $alumno['cve_uaslp'] ?? null;

                if (empty($claveAlumno)) {
                    throw ValidationException::withMessages([
                        'clave_alumno' => 'No se encontró la clave del alumno.'
                    ]);
                }

                // Validar solicitud activa
                $solicitudActiva = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                    ->orderByDesc('Fecha_Solicitud')
                    ->first();

                if ($solicitudActiva && (
                    $solicitudActiva->Estado_Departamento === 'pendiente' ||
                    $solicitudActiva->Estado_Encargado === 'pendiente' ||
                    ($solicitudActiva->Estado_Departamento === 'aprobado' &&
                    $solicitudActiva->Estado_Encargado === 'aprobado')
                )) {
                    return redirect()->back()->with('error', '⚠️ Ya tienes una solicitud en proceso.');
                }

                // ============================================
                // 1️⃣ GUARDAR ASESOR EXTERNO
                // ============================================
                $asesorExterno = null;
                if ($request->filled('nombre_asesor')) {
                    $asesorExterno = AsesorExterno::create([
                        'Nombre' => $request->nombre_asesor ?? '',
                        'Apellido_Paterno' => $request->apellido_paterno_asesor ?? '',
                        'Apellido_Materno' => $request->apellido_materno_asesor ?? '',
                        'Area' => $request->area_asesor ?? '',
                        'Puesto' => $request->puesto_asesor ?? '',
                        'Correo' => $request->correo_asesor ?? '',
                        'Telefono' => $request->telefono_asesor ?? '',
                    ]);
                }

                // ============================================
                // 2️⃣ PROCESAR DÍAS DE ASISTENCIA
                // ============================================
                $diasSeleccionados = $request->input('dias_asistencia', []);
                $diasString = is_array($diasSeleccionados) ? implode(',', $diasSeleccionados) : '';

                // ============================================
                // 3️⃣ GUARDAR ARCHIVOS PDF
                // ============================================
                $nombreArchivoConstancia = null;
                if ($request->constancia_derechos === 'si' && $request->hasFile('constancia_pdf')) {
                    $file = $request->file('constancia_pdf');
                    if ($file->isValid()) {
                        $nombreArchivoConstancia = $claveAlumno . '_carta_vigencia_derechos_' . time() . '.' . $file->getClientOriginalExtension();
                        if (!Storage::disk('public')->exists('expedientes/carta-vigencia-derechos')) {
                            Storage::disk('public')->makeDirectory('expedientes/carta-vigencia-derechos');
                        }
                        Storage::disk('public')->putFileAs('expedientes/carta-vigencia-derechos', $file, $nombreArchivoConstancia);
                    }
                }

                $nombreArchivoEstadistica = null;
                if ($request->estadistica_general === 'si' && $request->hasFile('estadistica_pdf')) {
                    $fileEstadistica = $request->file('estadistica_pdf');
                    if ($fileEstadistica->isValid()) {
                        $nombreArchivoEstadistica = $claveAlumno . '_estadistica_general_' . time() . '.' . $fileEstadistica->getClientOriginalExtension();
                        if (!Storage::disk('public')->exists('expedientes/estadistica-general')) {
                            Storage::disk('public')->makeDirectory('expedientes/estadistica-general');
                        }
                        Storage::disk('public')->putFileAs('expedientes/estadistica-general', $fileEstadistica, $nombreArchivoEstadistica);
                    }
                }

                $nombreArchivoCartaPasante = null;
                if ($request->has('cartapasante') && $request->hasFile('cartapasante_pdf')) {
                    $fileCartaPasante = $request->file('cartapasante_pdf');
                    if ($fileCartaPasante->isValid()) {
                        $nombreArchivoCartaPasante = $claveAlumno . '_carta_pasante_' . time() . '.' . $fileCartaPasante->getClientOriginalExtension();
                        if (!Storage::disk('public')->exists('expedientes/carta-pasante')) {
                            Storage::disk('public')->makeDirectory('expedientes/carta-pasante');
                        }
                        Storage::disk('public')->putFileAs('expedientes/carta-pasante', $fileCartaPasante, $nombreArchivoCartaPasante);
                    }
                }

                // ============================================
                // 4️⃣ CREAR SOLICITUD
                // ============================================
                $turno = $request->turno === 'M' ? 'M' : 'V';

                $solicitud = SolicitudFPP01::create([
                    'Fecha_Solicitud' => now(),
                    'Numero_Creditos' => $alumno['creditos'] ?? null,
                    'Induccion_Platicas' => $request->induccionpp === 'si' ? 1 : 0,
                    'Clave_Alumno' => $claveAlumno,
                    'Tipo_Seguro' => $request->has('tipo_seguro') ? 1 : 0,
                    'NSF' => $request->nsf,
                    'Situacion_Alumno_Pasante' => $request->estado === 'alumno' ? 1 : 2,
                    'Estadistica_General' => $request->estadistica_general === 'si' ? 1 : 0,
                    'Constancia_Vig_Der' => $request->constancia_derechos === 'si' ? 1 : 0,
                    'Carta_Pasante' => $request->has('cartapasante') ? 1 : 0,
                    'Egresado_Sit_Esp' => $request->has('egresadosit') ? 1 : 0,
                    'Archivo_CVD' => $request->hasFile('constancia_pdf') ? 1 : 0,
                    'Fecha_Inicio' => $request->fecha_inicio,
                    'Fecha_Fin' => $request->fecha_termino, // ⭐ Cambiado de Fecha_Termino
                    'Clave_Encargado' => 1,
                    'Clave_Asesor_Externo' => $asesorExterno ? $asesorExterno->Id_Asesor_Externo : null, // ⭐ ID real
                    'Datos_Asesor_Externo' => $asesorExterno ? 1 : 0,
                    'Productos_Servicios_Emp' => 'No se',
                    'Datos_Empresa' => 1,
                    'Nombre_Proyecto' => $request->nombre_proyecto, // ⭐ Corregido
                    'Actividades' => $request->actividades,
                    'Horario_Mat_Ves' => $turno,
                    'Horario_Entrada' => $request->horario_entrada,
                    'Horario_Salida' => $request->horario_salida,
                    'Dias_Semana' => $diasString, // ⭐ Con comas: "L,M,Mi,J,V"
                    'Validacion_Creditos' => $request->val_creditos === 'si' ? 1 : 0,
                    'Apoyo_Economico' => $request->apoyoeco === 'si' ? 1 : 0,
                    'Extension_Practicas' => $request->extension === 'si' ? 1 : 0,
                    'Expedicion_Recibos' => $request->expe_rec === 'si' ? 1 : 0,
                    'Autorizacion' => null,
                    'Propuso_Empresa' => 0,
                    'Evaluacion' => 0,
                    'Cancelar' => 0,
                    'Estado_Encargado' => 'pendiente',
                    'Estado_Departamento' => 'pendiente',
                ]);

                // ============================================
                // 5️⃣ PROCESAR SECTORES
                // ============================================
                $sectorPrivado = NULL;
                $sectorPublico = NULL;
                $sectorUASLP = NULL;
                $idEmpresa = null;

                if ($request->sector === 'privado') {
                    $nombreEmpresa = $request->nombre_empresa_privado;
                    $clasificacion = '1';
                    $rfc = $request->rfc_privado;
                    $ramo = $request->ramo_privado;
                    $calle = $request->calle_empresa_privado;
                    $numero = $request->numero_empresa_privado;
                    $colonia = $request->colonia_empresa_privado;
                    $cp = $request->cp_empresa_privado;
                    $estado = $request->estado_empresa_privado;
                    $municipio = $request->municipio_empresa_privado;
                    $telefono = $request->telefono_empresa_privado;

                    $sector = SectorPrivado::create([
                        'Area_Depto' => $request->area_depto_privado,
                        'Num_Trabajadores' => $request->num_trabajadores,
                        'Actividad_Giro' => $request->actividad_giro,
                        'Razon_Social' => $request->razon_social,
                        'Emp_Outsourcing' => $request->empresa_outsourcing === 'si' ? 1 : 0,
                        'Razon_Social_Outsourcing' => $request->razon_social_outsourcing
                    ]);
                    $sectorPrivado = $sector->Id_Privado;
                    
                } elseif ($request->sector === 'publico') {
                    $nombreEmpresa = $request->nombre_empresa_publico;
                    $clasificacion = '0';
                    $rfc = $request->rfc_publico;
                    $ramo = $request->ramo_publico;
                    $calle = $request->calle_empresa_publico;
                    $numero = $request->numero_empresa_publico;
                    $colonia = $request->colonia_empresa_publico;
                    $cp = $request->cp_empresa_publico;
                    $estado = $request->estado_empresa_publico;
                    $municipio = $request->municipio_empresa_publico;
                    $telefono = $request->telefono_publico; // ⭐ Ahora existe

                    $sector = SectorPublico::create([
                        'Area_Depto' => $request->area_depto_publico,
                        'Ambito' => $request->ambito,
                        'Telefono' => $telefono, // ⭐ AGREGAR TELÉFONO
                    ]);
                    $sectorPublico = $sector->Id_Publico;
                    
                } elseif ($request->sector === 'uaslp') {
                    $sector = SectorUaslp::create([
                        'Area_Depto' => $request->area_depto_uaslp,
                        'Tipo_Entidad' => $request->tipo_entidad,
                        'Id_Entidad_Academica' => $request->entidad_academica
                    ]);
                    $sectorUASLP = $sector->Id_UASLP;
                }

                // Crear o usar empresa existente
                if ($request->filled('empresa_registrada')) {
                    $idEmpresa = $request->empresa_registrada;
                } elseif ($request->sector === 'privado' || $request->sector === 'publico') {
                    $empresa = DependenciaEmpresa::create([
                        'Nombre_Depn_Emp' => $nombreEmpresa,
                        'Clasificacion' => $clasificacion,
                        'Calle' => $calle,
                        'Numero' => $numero,
                        'Colonia' => $colonia,
                        'Cp' => $cp,
                        'Estado' => $estado,
                        'Municipio' => $municipio,
                        'Telefono' => $telefono,
                        'Ramo' => $ramo,
                        'RFC_Empresa' => $rfc,
                        'Autorizada' => 0
                    ]);
                    $idEmpresa = $empresa->Id_Depn_Emp;
                }

                // Crear relación
                DependenciaMercadoSolicitud::create([
                    'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01,
                    'Id_Depend_Emp' => $idEmpresa,
                    'Id_Publico' => $sectorPublico,
                    'Id_Privado' => $sectorPrivado,
                    'Id_UASLP' => $sectorUASLP,
                    'Id_Mercado' => 1,
                    'Porcentaje' => 100
                ]);

                // ============================================
                // 6️⃣ ESTADO PROCESO
                // ============================================
                if (EstadoProceso::where('clave_alumno', $claveAlumno)->count() == 0) {
                    $etapas = [
                        'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                        'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                        'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                        'CARTA DE PRESENTACIÓN (DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES)',
                        'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)',
                        'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                        'CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)',
                        'CARTA DE PRESENTACIÓN (ALUMNO)',
                        'CARTA DE ACEPTACIÓN (ALUMNO)',
                        'SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA',
                        'CARTA DE DESGLOSE DE PERCEPCIONES',
                        'CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)',
                        'RECIBO DE PAGO',
                        'REPORTE PARCIAL NO. X',
                        'REVISIÓN REPORTE PARCIAL NO. X',
                        'REVISIÓN REPORTE FINAL',
                        'REPORTE FINAL',
                        'CORRECCIÓN REPORTE PARCIAL NO. X',
                        'CORRECCIÓN REPORTE FINAL',
                        'CALIFICACIÓN REPORTE FINAL',
                        'CARTA DE TÉRMINO',
                        'EVALUACIÓN DEL ALUMNO',
                        'CALIFICACIÓN FINAL',
                        'EVALUACIÓN DE LA EMPRESA',
                        'LIBERACIÓN DEL ALUMNO',
                        'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES',
                        'DOCUMENTO EXTRA (EJEMPLO)',
                    ];

                    foreach ($etapas as $etapa) {
                        EstadoProceso::create([
                            'clave_alumno' => $claveAlumno,
                            'etapa' => $etapa,
                            'estado' => 'pendiente'
                        ]);
                    }
                }

                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->whereIn('etapa', [
                        'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                        'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                        'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                        'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                    ])
                    ->update(['estado' => 'pendiente']);

                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES')
                    ->update(['estado' => 'proceso']);
            });

            $this->logBitacora("Registro de solicitud");
            return redirect()->route('alumno.inicio')->with('success', 'Solicitud guardada correctamente.');

        } catch (\Exception $e) {
            Log::error('Error en solicitud: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar la solicitud: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        // Obtener los datos del alumno desde sesión
        $alumno = session('alumno');

        if (!$alumno) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión primero.']);
        }

        // Buscar todas las solicitudes del alumno
        $solicitudes = \App\Models\SolicitudFPP01::where('Clave_Alumno', $alumno['cve_uaslp'])
            ->orderBy('Fecha_Solicitud', 'desc')
            ->get();

        // Enviar a la vista
        return view('alumno.expediente.solicitudFPP01', compact('solicitudes'));
    }

    public function show($id)
    {
        $alumno = session('alumno');

        if (!$alumno) {
            abort(403, 'Debes iniciar sesión para ver esta solicitud.');
        }

        $solicitud = SolicitudFPP01::findOrFail($id);

        // Todo correcto, mostrar la vista
        return view('alumno.expediente.verSolicitud', compact('solicitud'));
    }

    public function create()
    {
        $alumno = session('alumno');

        if (!$alumno) {
            return redirect()->route('alumno.inicio')->with('error', 'Sesión no válida.');
        }

        $claveAlumno = $alumno['cve_uaslp'];

        // Traer la última solicitud del alumno
        $ultima = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->latest('Fecha_Solicitud')
            ->first();

        // Validar si ya tiene una solicitud activa
        if ($ultima && (
            $ultima->Estado_Departamento == 'pendiente' ||
            $ultima->Estado_Encargado == 'pendiente' ||
            ($ultima->Estado_Departamento == 'aprobado' && $ultima->Estado_Encargado == 'aprobado')
        )) {
            return redirect()->route('alumno.estado')
                ->with('error', 'Ya tienes una solicitud en revisión. No puedes crear otra hasta que sea rechazada.');
        }

        // Mostrar formulario solo si NO está limitada
        $solicitudes = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->orderByDesc('Fecha_Solicitud')
            ->get();

        $carreras = CarreraIngenieria::select('Descripcion_Capitalizadas')
                        ->distinct()
                        ->orderBy('Descripcion_Capitalizadas')
                        ->get();

        // Obtener todas las empresas para el menú desplegable
        $empresas = \App\Models\DependenciaEmpresa::orderBy('Nombre_Depn_Emp')->get();

        return view('alumno.expediente.solicitudFPP01', compact('solicitudes', 'empresas'));
    }

    public function edit($id)
    {
        $alumno = session('alumno');

        if (!$alumno) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión primero.']);
        }

        $solicitud = SolicitudFPP01::findOrFail($id);

        if ($solicitud->Clave_Alumno !== $alumno['cve_uaslp']) {
            abort(403, 'No tienes permiso para editar esta solicitud.');
        }

        if ($solicitud->Estatus !== 'rechazada') {
            return redirect()->route('solicitud.ver', $id)->with('info', 'Solo las solicitudes rechazadas se pueden corregir.');
        }

        return view('alumno.expediente.editarSolicitud', compact('solicitud'));
    }
}
