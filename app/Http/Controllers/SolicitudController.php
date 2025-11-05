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
                // Primero obtener el alumno desde sesión o request
                $alumno = session('alumno');
                $claveAlumno = $alumno['cve_uaslp'] ?? $request->input('clave') ?? $request->input('clave_hidden') ?? null;

                //  Validación: impedir enviar nueva solicitud si ya existe una en proceso o aprobada
                $solicitudActiva = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                    ->orderByDesc('Fecha_Solicitud')
                    ->first();

                if ($solicitudActiva) {
                    // Si está pendiente o aprobada, bloquear
                    if (
                        $solicitudActiva->Estado_Departamento === 'pendiente' ||
                        $solicitudActiva->Estado_Encargado === 'pendiente' ||
                        ($solicitudActiva->Estado_Departamento === 'aprobado' &&
                        $solicitudActiva->Estado_Encargado === 'aprobado')
                    ) {
                        return redirect()->back()->with('error', '⚠️ Ya tienes una solicitud en proceso. Espera la resolución antes de enviar otra.');
                    }
                    // Si la rechazaron cualquiera de los dos → puede volver a mandar
                }

                if (empty($claveAlumno)) {
                    Log::warning('Intento de guardar solicitud sin Clave_Alumno. Session alumno: ', ['session_alumno' => $alumno]);
                    throw ValidationException::withMessages([
                        'clave_alumno' => 'No se encontró la clave del alumno. Asegúrate de haber iniciado sesión o de que el web service devuelva la información.'
                    ]);
                }

                // Guardar PDF de constancia de vigencia de derechos si corresponde
                $nombreArchivoConstancia = null;
                if ($request->constancia_derechos === 'si' && $request->hasFile('constancia_pdf')) {
                    $file = $request->file('constancia_pdf');
                    if ($file->isValid()) {
                        $nombreArchivoConstancia = $claveAlumno . '_carta_vigencia_derechos_' . time() . '.' . $file->getClientOriginalExtension();
                        $ruta = 'expedientes/carta-vigencia-derechos/' . $nombreArchivoConstancia;
                        if (!Storage::disk('public')->exists('expedientes/carta-vigencia-derechos')) {
                            Storage::disk('public')->makeDirectory('expedientes/carta-vigencia-derechos');
                        }
                        Storage::disk('public')->putFileAs('expedientes/carta-vigencia-derechos', $file, $nombreArchivoConstancia);
                    }
                }

                // Guardar PDF de estadística general si corresponde
                $nombreArchivoEstadistica = null;
                if ($request->estadistica_general === 'si' && $request->hasFile('estadistica_pdf')) {
                    $fileEstadistica = $request->file('estadistica_pdf');
                    if ($fileEstadistica->isValid()) {
                        $nombreArchivoEstadistica = $claveAlumno . '_estadistica_general_' . time() . '.' . $fileEstadistica->getClientOriginalExtension();
                        $rutaEstadistica = 'expedientes/estadistica-general/' . $nombreArchivoEstadistica;
                        if (!Storage::disk('public')->exists('expedientes/estadistica-general')) {
                            Storage::disk('public')->makeDirectory('expedientes/estadistica-general');
                        }
                        Storage::disk('public')->putFileAs('expedientes/estadistica-general', $fileEstadistica, $nombreArchivoEstadistica);
                    }
                }

                // Guardar PDF de carta pasante si corresponde
                $nombreArchivoCartaPasante = null;
                if ($request->has('cartapasante') && $request->hasFile('cartapasante_pdf')) {
                    $fileCartaPasante = $request->file('cartapasante_pdf');
                    if ($fileCartaPasante->isValid()) {
                        $nombreArchivoCartaPasante = $claveAlumno . '_carta_pasante_' . time() . '.' . $fileCartaPasante->getClientOriginalExtension();
                        $rutaCartaPasante = 'expedientes/carta-pasante/' . $nombreArchivoCartaPasante;
                        if (!Storage::disk('public')->exists('expedientes/carta-pasante')) {
                            Storage::disk('public')->makeDirectory('expedientes/carta-pasante');
                        }
                        Storage::disk('public')->putFileAs('expedientes/carta-pasante', $fileCartaPasante, $nombreArchivoCartaPasante);
                    }
                }

                // Preparar variables de días y turno
                $diasSeleccionados = (array) $request->input('dias_asistencia', []);
                $diasString = implode('', $diasSeleccionados);
                $turno = $request->turno === 'M' ? 'M' : 'V';

                // Crear la solicitud usando los datos del alumno
                $solicitud = SolicitudFPP01::create([
                    'Fecha_Solicitud' => now(),
                    'Numero_Creditos' => $alumno['creditos'] ?? null,
                    'Induccion_Platicas' => $request->induccionpp === 'si' ? 1 : 0,
                    'Clave_Alumno' => $claveAlumno,
                    'Tipo_Seguro' => $request->has('tipo_seguro') ? 1 : 0,
                    'NSF' => $request->nsf,
                    //1 - Alumno
                    //2 - Pasante
                    'Situacion_Alumno_Pasante' => $request->estado === 'alumno' ? 1 : 2,

                    'Estadistica_General' => $request->estadistica_general === 'si' ? 1 : 0,
                    'Constancia_Vig_Der' => $request->constancia_derechos === 'si' ? 1 : 0,
                    'Carta_Pasante' => $request->has('cartapasante') ? 1 : 0,
                    'Egresado_Sit_Esp' => $request->has('egresadosit') ? 1 : 0,
                    'Archivo_CVD' => $request->hasFile('constancia_pdf') ? 1 : 0,
                    'Fecha_Inicio' => $request->fecha_inicio, //No lo he checado
                    'Fecha_Termino' => $request->fecha_termino, //No lo he checado
                    'Clave_Encargado' => 1, //Nos falta este dato del web service
                    'Clave_Asesor_Externo' => 1, //Nos falta este dato (No sabemos como manejarlo)
                    'Datos_Asesor_Externo' => 1, //Nos falta este dato (No sabemos como manejarlo)
                    'Productos_Servicios_Emp' => 'No se', //Nos falta este dato (No sabemos como manejarlo)
                    'Datos_Empresa' => 1, //Nos falta este dato (No sabemos como manejarlo)
                    'Nombre_Proyecto' => $request->mombre_proyecto,
                    'Actividades' => $request->actividades,
                    'Horario_Mat_Ves' => $turno,
                    'Horario_Entrada' => $request->horario_entrada,  //HH:MM
                    'Horario_Salida' => $request->horario_salida,   //HH:MM
                    'Dias_Semana' => $diasString,
                    'Validacion_Creditos' => $request->val_creditos === 'si' ? 1 : 0,
                    'Apoyo_Economico' => $request->apoyoeco === 'si' ? 1 : 0,
                    'Extension_Practicas' => $request->extension === 'si' ? 1 : 0,
                    'Expedicion_Recibos' => $request->expe_rec === 'si' ? 1 : 0,
                    'Autorizacion' => null,
                    'Propuso_Empresa' => 0, //Nos falta este dato (No sabemos como manejarlo)
                    'Evaluacion' => 0, //Nos falta este dato (No sabemos como manejarlo)
                    'Cancelar' => 0, //Nos falta este dato (No sabemos como manejarlo)
                    'Estado_Encargado' => 'pendiente',
                    'Estado_Departamento' => 'pendiente',
                ]);

                // 🔹 Verifica si el alumno ya tiene etapas en estado_proceso
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

                // Reiniciar las etapas principales si ya existían (para nueva solicitud)
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->whereIn('etapa', [
                        'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                        'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                        'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                        'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                    ])
                    ->update(['estado' => 'pendiente']);

                // Luego de eso, marca la primera etapa como "proceso"
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES')
                    ->update(['estado' => 'proceso']);

                // Asegurar que DSSPP también se reinicie (por si estaba realizado)
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'pendiente']);

                $sectorPrivado = NULL; // Id de los sectores que no existen
                $sectorPublico = NULL; // Id de los sectores que no existen
                $sectorUASLP = NULL; // Id de los sectores que no existen
                if ($request->sector === 'privado') {
                    $nombreEmpresa = $request->nombre_empresa_privado;
                    $clasificacion = '1'; //Vamos a cambiar el tipo de dato a int, por ahora regresa un varchar
                    $rfc = $request->rfc_privado;
                    $ramo = $request->ramo_privado;
                    $calle = $request->calle_empresa_privado;
                    $numero = $request->numero_empresa_privado;
                    $colonia = $request->colonia_empresa_privado;
                    $cp = $request->cp_empresa_privado;
                    $estado = $request->estado_empresa_privado;
                    $municipio = $request->municipio_empresa_privado;
                    $telefono = $request->telefono_empresa_privado;
                    $areaDepto = $request->area_depto_privado;

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
                    $clasificacion = '0'; //Vamos a cambiar el tipo de dato a int, por ahora regresa un varchar
                    $rfc = $request->rfc_publico;
                    $ramo = $request->ramo_publico;
                    $calle = $request->calle_empresa_publico;
                    $numero = $request->numero_empresa_publico;
                    $colonia = $request->colonia_empresa_publico;
                    $cp = $request->cp_empresa_publico;
                    $estado = $request->estado_empresa_publico;
                    $municipio = $request->municipio_empresa_publico;
                    $telefono = $request->telefono_empresa_publico;
                    $areaDepto = $request->area_depto_publico;

                    $sector = SectorPublico::create([
                        'Area_Depto' => $request->area_depto_publico,
                        'Ambito' => $request->ambito
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

                $idEmpresa = NULL;
                if ($request->sector === 'privado' || $request->sector === 'publico') {
                    // Crear empresa
                    $empresa = DependenciaEmpresa::create([
                        'Nombre_Depn_Emp' => $nombreEmpresa,
                        //Se refiere a si se trata de una dependencia (sector público) o a una empresa (sector privado).
                        //0 dependencia
                        //1 empresa
                        'Clasificacion' => $clasificacion, //Vamos a cambiar el tipo de dato a int, por ahora regresa un varchar
                        'Calle' => $calle,
                        'Numero' => $numero,
                        'Colonia' => $colonia,
                        'Cp' => $cp,
                        'Estado' => $estado,
                        'Municipio' => $municipio,
                        'Telefono' => $telefono,
                        //1: Agricultura, ganadería y caza; 2: Transporte y comunicaciones; 3: Industria manufacturera; 4: Restaurantes y hoteles; 5: Servicios profesionales y técnicos especializados; 6: Servicios de reparación y mantenimiento; 7: Servicios educativos; 8: Construcción; 9: Otro
                        'Ramo' => $ramo,
                        'RFC_Empresa' => $rfc,
                        //0 no
                        //1 si
                        'Autorizada' => 0
                    ]);
                    $idEmpresa = $empresa->Id_Depn_Emp;
                }

                // Crear relación mercado/dependencia
                DependenciaMercadoSolicitud::create([
                    'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01,
                    'Id_Depend_Emp' => isset($empresa) ? $empresa->Id_Depn_Emp : null,
                    'Id_Publico' => $sectorPublico ?? null,
                    'Id_Privado' => $sectorPrivado ?? null,
                    'Id_UASLP' => $sectorUASLP ?? null,
                    'Id_Mercado' => 1,
                    'Porcentaje' => 100
                ]);
            });

            $this->logBitacora("Registro de solicitud");
            return redirect()->route('alumno.inicio')->with('success', 'Solicitud guardada correctamente.');

        } catch (\Exception $e) {
            dd('Error guardando solicitud: ' . $e->getMessage());
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
