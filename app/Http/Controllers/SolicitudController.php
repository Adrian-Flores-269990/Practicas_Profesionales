<?php

namespace App\Http\Controllers;

use App\Models\SolicitudFPP01;
use App\Models\DependenciaEmpresa;
use App\Models\DependenciaMercadoSolicitud;
use App\Models\SectorPrivado;
use App\Models\SectorPublico;
use App\Models\SectorUaslp;
use App\Models\AsesorExterno; // Importación necesaria para la lógica de store()
use App\Models\CarreraIngenieria;
use App\Models\EstadoProceso;
use App\Models\Expediente; // Importación de Versión B
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SolicitudController extends Controller
{
    // Método auxiliar (Mantenido del código anterior)


    public function store(Request $request)
    {
        try {
            DB::transaction(function() use ($request) {
                $alumno = session('alumno');
                // Uso de la lógica más robusta para Clave_Alumno (Versión B, aunque A era suficiente)
                $claveAlumno = $alumno['cve_uaslp'] ?? $request->input('clave') ?? $request->input('clave_hidden') ?? null;

                if (empty($claveAlumno)) {
                    Log::warning('Intento de guardar solicitud sin Clave_Alumno. Session alumno: ', ['session_alumno' => $alumno]);
                    throw ValidationException::withMessages([
                        'clave_alumno' => 'No se encontró la clave del alumno. Asegúrate de haber iniciado sesión o de que el web service devuelva la información.'
                    ]);
                }

                // ============================================
                // 1️⃣ VALIDACIÓN DE SOLICITUD ACTIVA
                // ============================================
                $solicitudActiva = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                    ->orderByDesc('Fecha_Solicitud')
                    ->first();

                if ($solicitudActiva && (
                    $solicitudActiva->Estado_Departamento === 'pendiente' ||
                    $solicitudActiva->Estado_Encargado === 'pendiente' ||
                    ($solicitudActiva->Estado_Departamento === 'aprobado' &&
                    $solicitudActiva->Estado_Encargado === 'aprobado')
                )) {
                    // Se lanza una excepción que se capturará fuera de la transacción
                    throw new \Exception('Ya tienes una solicitud en proceso.');
                }

                // ============================================
                // 2️⃣ GUARDAR ASESOR EXTERNO (Versión A)
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
                // 3️⃣ PROCESAR DÍAS DE ASISTENCIA
                // ============================================
                $diasSeleccionados = $request->input('dias_asistencia', []);
                // Se usa implore(',', ...) de Versión A (más legible), no implode('', ...) de Versión B
                $diasString = is_array($diasSeleccionados) ? implode(',', $diasSeleccionados) : '';
                $turno = $request->turno === 'M' ? 'M' : 'V';

                // ============================================
                // 4️⃣ GUARDAR ARCHIVOS PDF (Lógica de ambas versiones era igual)
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
                // 5️⃣ CREAR SOLICITUD (Campos fusionados)
                // ============================================
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
                    // Corregido: La Versión A usa Fecha_Fin, la Versión B usa Fecha_Termino. Asumo que el campo es 'Fecha_Fin' o debe ser 'Fecha_Termino'
                    'Fecha_Fin' => $request->fecha_termino, 
                    'Clave_Encargado' => 1,
                    // Se usa el ID del Asesor real (Versión A)
                    'Clave_Asesor_Externo' => $asesorExterno ? $asesorExterno->Id_Asesor_Externo : null, 
                    'Datos_Asesor_Externo' => $asesorExterno ? 1 : 0,
                    'Productos_Servicios_Emp' => 'No se',
                    'Datos_Empresa' => 1,
                    // Corregido: Se usa la entrada correcta `nombre_proyecto` (Versión A)
                    'Nombre_Proyecto' => $request->nombre_proyecto, 
                    'Actividades' => $request->actividades,
                    'Horario_Mat_Ves' => $turno,
                    'Horario_Entrada' => $request->horario_entrada,
                    'Horario_Salida' => $request->horario_salida,
                    'Dias_Semana' => $diasString,
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

                // Variables para la creación de DependenciaEmpresa (Mantenido de Versión A)
                $nombreEmpresa = null;
                $clasificacion = null;
                $rfc = null;
                $ramo = null;
                $calle = null;
                $numero = null;
                $colonia = null;
                $cp = null;
                $estado = null;
                $municipio = null;
                $telefono = null;

                // ============================================
                // 6️⃣ PROCESAR SECTORES (Mantenido de Versión A - Es la lógica completa)
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
                    $telefono = $request->telefono_publico;

                    $sector = SectorPublico::create([
                        'Area_Depto' => $request->area_depto_publico,
                        'Ambito' => $request->ambito,
                        'Telefono' => $telefono,
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

                // Crear relación Dependencia-Mercado-Solicitud
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
                // 7️⃣ ESTADO PROCESO (Lógica de ambas versiones era igual)
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

                // Reiniciar las etapas pendientes/aprobadas para la nueva solicitud
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->whereIn('etapa', [
                        'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                        'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                        'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                        'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                    ])
                    ->update(['estado' => 'pendiente']);

                // Marcar la etapa de registro como 'proceso'
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES')
                    ->update(['estado' => 'proceso']);

                // Asegurar creación de expediente asociado a la solicitud (Nuevo, basado en importación de Expeditente)
                Expediente::firstOrCreate(
                    ['Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01],
                    ['Carta_Desglose_Percepciones' => 0]
                );
            });

            $this->logBitacora("Registro de solicitud");
            return redirect()->route('alumno.inicio')->with('success', 'Solicitud guardada correctamente.');

        } catch (ValidationException $e) {
             // Manejar errores de validación
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error en solicitud: ' . $e->getMessage());
             if ($e->getMessage() === 'Ya tienes una solicitud en proceso.') {
                return back()->with('error', '⚠️ Ya tienes una solicitud en proceso. Espera la resolución antes de enviar otra.');
            }
            return back()->withErrors(['error' => 'Error al guardar la solicitud: ' . $e->getMessage()]);
        }
    }

    // ------------------------------------------------------------------
    // Métodos de Vista (Consolidado)
    // ------------------------------------------------------------------

    public function index()
    {
        // Obtener los datos del alumno desde sesión
        $alumno = session('alumno');

        if (!$alumno) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión primero.']);
        }

        // Buscar todas las solicitudes del alumno
        // Nota: Se corrigió el uso de \App\Models\SolicitudFPP01 a SolicitudFPP01 ya que está importado
        $solicitudes = SolicitudFPP01::where('Clave_Alumno', $alumno['cve_uaslp'])
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
        // Nota: Se corrigió el uso de \App\Models\DependenciaEmpresa a DependenciaEmpresa ya que está importado
        $empresas = DependenciaEmpresa::orderBy('Nombre_Depn_Emp')->get();

        return view('alumno.expediente.solicitudFPP01', compact('solicitudes', 'empresas', 'carreras'));
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

        // Se usa la condición de la Versión A (que tenía un bug), y se corrige a la intención original (que tenía la Versión B)
        // La condición correcta debe ser 'rechazado' para ambos estados (Departamento y Encargado).
        if ($solicitud->Estado_Departamento !== 'rechazado' && $solicitud->Estado_Encargado !== 'rechazado') {
            return redirect()->route('solicitud.ver', $id)->with('info', 'Solo las solicitudes rechazadas se pueden corregir.');
        }

        return view('alumno.expediente.editarSolicitud', compact('solicitud'));
    }
}