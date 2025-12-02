<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SolicitudFPP01;
use App\Models\SolicitudFPP02;
use App\Models\AutorizacionSolicitud;
use App\Models\EstadoProceso;
use App\Models\CarreraIngenieria;
use App\Models\Alumno;
use App\Services\UaslpApiService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Expediente;
use App\Models\Reporte;
use Carbon\Carbon;
use App\Models\Evaluacion;
use App\Models\Respuesta;
use App\Models\Pregunta;

class EncargadoController extends Controller
{
    protected $uaslpApi;

    public function __construct(UaslpApiService $uaslpApi)
    {
        $this->uaslpApi = $uaslpApi;
    }

    public function index()
    {
        $solicitudes = SolicitudFPP01::with(['alumno', 'autorizaciones'])
            ->whereHas('autorizaciones', function ($q) {
                $q->whereNotNull('Autorizo_Empleado');
            })
            ->get();

        $carreras = CarreraIngenieria::orderBy('Descripcion_Capitalizadas')->get();

        return view('encargado.solicitudes_alumnos', compact('solicitudes', 'carreras'));
    }

    /**
     * Consultar alumno por clave o apellidos
     */
    public function consultarAlumno(Request $request)
    {
        $alumnos = [];
        $documentos = [];

        if ($request->filled('busqueda')) {
            $busqueda = trim($request->busqueda);

            // Variante para buscar FPP01 por clave sin el PRIMER 0 a la izquierda (si lo tiene)
            $busquedaClaveSinPrimerCero = $busqueda;
            if (preg_match('/^0\d+$/', $busqueda)) {
                // quitar solo el primer 0
                $busquedaClaveSinPrimerCero = substr($busqueda, 1);
            }

            // Buscar alumnos con solicitudes que coincidan por clave o apellidos
            $alumnosQuery = Alumno::whereHas('solicitudes')
                ->where(function($query) use ($busqueda, $busquedaClaveSinPrimerCero) {
                    // Clave exacta ingresada o sin el primer 0 a la izquierda
                    $query->where(function($q) use ($busqueda, $busquedaClaveSinPrimerCero) {
                        $q->where('Clave_Alumno', 'LIKE', "%{$busqueda}%");
                        if ($busquedaClaveSinPrimerCero !== $busqueda) {
                            $q->orWhere('Clave_Alumno', 'LIKE', "%{$busquedaClaveSinPrimerCero}%");
                        }
                    })
                    // Búsqueda por apellidos/nombre con el texto tal cual se ingresó
                    ->orWhere('ApellidoP_Alumno', 'LIKE', "%{$busqueda}%")
                    ->orWhere('ApellidoM_Alumno', 'LIKE', "%{$busqueda}%")
                    ->orWhere(DB::raw("CONCAT(ApellidoP_Alumno, ' ', ApellidoM_Alumno)"), 'LIKE', "%{$busqueda}%")
                    ->orWhere(DB::raw("CONCAT(Nombre, ' ', ApellidoP_Alumno, ' ', ApellidoM_Alumno)"), 'LIKE', "%{$busqueda}%");
                })
                ->with('solicitudes')
                ->limit(10)
                ->get();

             // Obtener los estados de proceso (semáforo) para todas las claves encontradas
            $claves = $alumnosQuery->pluck('Clave_Alumno')->toArray();
            $estadosProceso = EstadoProceso::whereIn('clave_alumno', $claves)
                ->orderBy('clave_alumno')
                ->orderBy('id')
                ->get()
                ->groupBy('clave_alumno');

            $alumnos = $alumnosQuery->map(function($alumno) use ($estadosProceso) {
                // Obtener la última (más reciente) solicitud FPP01 del alumno para resumen
                $solicitud = $alumno->solicitudes->load('dependenciaMercadoSolicitud.dependenciaEmpresa')
                    ->sortByDesc(function($s){
                        // Usa fecha solicitud si existe, si no el id como fallback
                        return $s->Fecha_Solicitud ?? $s->Id_Solicitud_FPP01;
                    })->first();

                $resumenSolicitud = null;
                $contadorReportes = 0;
                $reportesPendientes = 0;

                if ($solicitud) {
                    // Obtener nombre de empresa desde la relación
                    $empresaNombre = null;
                    if ($solicitud->dependenciaMercadoSolicitud && $solicitud->dependenciaMercadoSolicitud->dependenciaEmpresa) {
                        $empresaNombre = $solicitud->dependenciaMercadoSolicitud->dependenciaEmpresa->Nombre_Depn_Emp;
                    }

                    // Construir horario desde los campos de la base de datos
                    $horario = null;
                    if ($solicitud->Horario_Mat_Ves || $solicitud->Horario_Entrada || $solicitud->Horario_Salida) {
                        $turno = $solicitud->Horario_Mat_Ves === 'M' ? 'Matutino' : ($solicitud->Horario_Mat_Ves === 'V' ? 'Vespertino' : '');
                        $horas = '';
                        if ($solicitud->Horario_Entrada && $solicitud->Horario_Salida) {
                            $horas = " ({$solicitud->Horario_Entrada} - {$solicitud->Horario_Salida})";
                        }
                        $dias = $solicitud->Dias_Semana ? " - {$solicitud->Dias_Semana}" : '';
                        $horario = trim($turno . $horas . $dias);
                    }

                    // Obtener información de reportes
                    $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();
                    if ($expediente) {
                        $contadorReportes = Reporte::where('Id_Expediente', $expediente->Id_Expediente)->count();
                        $reportesPendientes = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
                            ->whereNull('Calificacion')
                            ->count();
                    }

                    // Construimos un resumen ligero evitando exponer todo el modelo
                    $resumenSolicitud = [
                        'id' => $solicitud->Id_Solicitud_FPP01 ?? null,
                        'fecha_registro' => $solicitud->Fecha_Solicitud ?? null,
                        'empresa' => $empresaNombre,
                        'proyecto' => $solicitud->Nombre_Proyecto ?? null,
                        'horario' => $horario,
                        'estado_encargado' => $solicitud->Estado_Encargado ?? null,
                        'autorizacion' => $solicitud->Autorizacion ?? null,
                    ];
                    // Buscar evaluación existente (Tipo_Evaluacion='Empresa') por solicitud o empresa
                    $tieneEvaluacion = false;
                    $evaluacionData = null;
                    try {
                        $evaluacion = Evaluacion::query()
                            ->where('Tipo_Evaluacion', 'Empresa')
                            ->where(function($q) use ($solicitud) {
                                $q->where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01);
                                if ($solicitud->dependenciaMercadoSolicitud && $solicitud->dependenciaMercadoSolicitud->Id_Depend_Emp) {
                                    $q->orWhere('Id_Depn_Emp', $solicitud->dependenciaMercadoSolicitud->Id_Depend_Emp);
                                }
                            })
                            ->with(['respuestas.pregunta'])
                            ->orderByDesc('Id_Evaluacion')
                            ->first();

                        if ($evaluacion) {
                            $tieneEvaluacion = true;
                            $fechaEvaluacion = null;
                            if (isset($evaluacion->created_at) && $evaluacion->created_at) {
                                try { $fechaEvaluacion = \Carbon\Carbon::parse($evaluacion->created_at)->format('d/m/Y H:i'); } catch (\Exception $e) { $fechaEvaluacion = null; }
                            }
                            $evaluacionData = [
                                'empresa' => $empresaNombre,
                                'proyecto' => $solicitud->Nombre_Proyecto ?? 'N/A',
                                'fecha' => $fechaEvaluacion ?? 'N/A',
                                'respuestas' => $evaluacion->respuestas->map(function($respuesta) {
                                    return [
                                        'pregunta' => $respuesta->pregunta ? $respuesta->pregunta->Pregunta : 'Pregunta no encontrada',
                                        'respuesta' => $respuesta->Respuesta ?? 'Sin respuesta',
                                    ];
                                })->toArray()
                            ];
                        }
                    } catch (\Throwable $e) {
                        // en caso de error, no bloquear la consulta
                        $tieneEvaluacion = false;
                        $evaluacionData = null;
                    }
                }

                $ordenEtapas = [
                    'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                    'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                    'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                    'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                    'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)',
                    'CARTA DE PRESENTACIÓN (DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES)',
                    'CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)',
                    'CARTA DE PRESENTACIÓN (ALUMNO)',
                    'CARTA DE ACEPTACIÓN (ALUMNO)',
                    'CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)',
                    'CARTA DE DESGLOSE DE PERCEPCIONES',
                    'SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA',
                    'RECIBO DE PAGO',
                    'REPORTE PARCIAL',
                    'REVISIÓN REPORTE PARCIAL',
                    'CORRECCIÓN REPORTE PARCIAL',
                    'REPORTE FINAL',
                    'REVISIÓN REPORTE FINAL',
                    'CORRECCIÓN REPORTE FINAL',
                    'CARTA DE TÉRMINO',
                    'EVALUACIÓN DE LA EMPRESA',
                    'CALIFICACIÓN FINAL',
                    'EVALUACIÓN DEL ALUMNO',
                    'LIBERACIÓN DEL ALUMNO',
                    'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES',
                    'DOCUMENTO EXTRA (EJEMPLO)',
                ];

                // Obtener estados y ORDENARLOS
                $semaforo = [];

                if (isset($estadosProceso[$alumno->Clave_Alumno])) {

                    $semaforo = $estadosProceso[$alumno->Clave_Alumno]
                        ->sortBy(function ($item) use ($ordenEtapas) {
                            return array_search($item->etapa, $ordenEtapas);
                        })
                        ->map(function ($estado) {
                            return [
                                'etapa' => $estado->etapa,
                                'estado' => $estado->estado,
                            ];
                        })
                        ->values()
                        ->toArray();
                }

                // Agregar información de reportes al semáforo si existe expediente
                if ($expediente && $contadorReportes > 0) {
                    $reportesCalificados = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
                        ->whereNotNull('Calificacion')
                        ->count();

                    $estadoReportes = 'pendiente';
                    if ($reportesCalificados == $contadorReportes && $contadorReportes > 0) {
                        $estadoReportes = 'realizado';
                    } elseif ($reportesCalificados > 0) {
                        $estadoReportes = 'proceso';
                    }

                    $semaforo[] = [
                        'etapa' => "Reportes mensuales ({$reportesCalificados}/{$contadorReportes} calificados)",
                        'estado' => $estadoReportes,
                    ];
                }

                return [
                    'cve_uaslp' => $alumno->Clave_Alumno,
                    'nombres' => $alumno->Nombre,
                    'paterno' => $alumno->ApellidoP_Alumno,
                    'materno' => $alumno->ApellidoM_Alumno,
                    'carrera' => $alumno->Carrera,
                    'semestre' => $alumno->Semestre,
                    'creditos' => $alumno->Creditos,
                    'correo' => $alumno->CorreoElectronico,
                    'solicitud_fpp01' => $resumenSolicitud,
                    'semaforo' => $semaforo,
                    'contador_reportes' => $contadorReportes,
                    'reportes_pendientes' => $reportesPendientes,
                    'tiene_evaluacion' => $tieneEvaluacion ?? false,
                    'evaluacion' => $evaluacionData,
                ];
            })->toArray();

            // Si la búsqueda parece ser una clave exacta y hay al menos un alumno que coincide por clave
            $posibleClave = $busqueda; // Para PDFs se busca TAL CUAL fue ingresada
            $claveDirecta = null;
            if (preg_match('/^\d{4,}$/', $posibleClave)) { // Ej. 194659
                $claveDirecta = $posibleClave;
            }

            if ($claveDirecta) {
                $documentos = $this->buscarDocumentosAlumno($claveDirecta);
            }
        }

        return view('encargado.consultar_alumno', compact('alumnos', 'documentos'));
    }

    /**
     * Busca en storage/public/expedientes/* los PDFs cuyo nombre inicia con la clave del alumno.
     */
    private function buscarDocumentosAlumno(string $claveAlumno): array
    {
        $base = 'expedientes';
        if (!Storage::disk('public')->exists($base)) {
            return [];
        }

        // Buscar recursivamente todos los archivos bajo expedientes
        $files = Storage::disk('public')->allFiles($base);

        $encontrados = [];
        foreach ($files as $file) {
            // Solo PDFs
            if (!Str::endsWith(strtolower($file), '.pdf')) continue;

            $filename = basename($file);

            // Coincidencia: empieza con "<clave>_"
            if (Str::startsWith($filename, $claveAlumno . '_')) {
                $encontrados[] = [
                    'path' => $file,
                    'titulo' => $this->tituloDocumento($filename),
                    'nombre' => $filename,
                    'size_kb' => round(Storage::disk('public')->size($file) / 1024, 1),
                    'url' => asset('storage/' . $file),
                    'modificado' => date('Y-m-d H:i', Storage::disk('public')->lastModified($file)),
                ];
                continue;
            }

            // Soporte adicional: algunos FPP02 se nombran como "FPP02_<clave>_..."
            if (Str::contains($filename, 'FPP02_' . $claveAlumno . '_')) {
                $encontrados[] = [
                    'path' => $file,
                    'titulo' => $this->tituloDocumento($filename),
                    'nombre' => $filename,
                    'size_kb' => round(Storage::disk('public')->size($file) / 1024, 1),
                    'url' => asset('storage/' . $file),
                    'modificado' => date('Y-m-d H:i', Storage::disk('public')->lastModified($file)),
                ];
            }
        }

        return collect($encontrados)->sortByDesc('modificado')->values()->toArray();
    }
    /**
     * Determina título legible según el nombre del archivo.
     */
    private function tituloDocumento(string $filename): string
    {
        $map = [
            '_desglose_percepciones_' => 'Carta de Desglose de Percepciones',
            '_carta_aceptacion_' => 'Carta de Aceptación',
            '_carta_vigencia_derechos_' => 'Constancia Vigencia de Derechos',
            '_estadistica_general_' => 'Estadística General',
            '_carta_pasante_' => 'Carta Pasante',
            '_FPP02_firmado_' => 'Formato FPP02 Firmado',
            'FPP02_' => 'Formato FPP02 Generado',
        ];
        foreach ($map as $needle => $titulo) {
            if (Str::contains($filename, $needle)) return $titulo;
        }
        return 'Documento';
    }

    public function verSolicitud($id)
    {
        $solicitud = SolicitudFPP01::findOrFail($id);

        // Bitácora: encargado abre solicitud
        $this->logBitacora("Encargado visualizó solicitud #$id");

        return view('encargado.revisar_solicitud', compact('solicitud'));
    }

    public function revisar($id)
    {
        $solicitud = SolicitudFPP01::with([
            'alumno',
            'dependenciaMercadoSolicitud.dependenciaEmpresa',
            'dependenciaMercadoSolicitud.sectorPrivado',
            'dependenciaMercadoSolicitud.sectorPublico',
            'dependenciaMercadoSolicitud.sectorUaslp',
            'autorizaciones'
        ])->findOrFail($id);

        // Bitácora: encargado inició revisión
        $this->logBitacora("Encargado revisa detalles de solicitud #$id");

        return view('encargado.revision', compact('solicitud'));
    }

    public function autorizarSolicitud(Request $request, $id)
    {
        $request->validate([
            'seccion_solicitante' => 'nullable|string',
            'seccion_empresa'     => 'nullable|string',
            'seccion_proyecto'    => 'nullable|string',
            'seccion_horario'     => 'nullable|string',
            'seccion_creditos'    => 'nullable|string',
            'comentario_encargado'=> 'nullable|string|max:1000',
        ]);

        $solicitud = SolicitudFPP01::with('autorizaciones')->findOrFail($id);

        // Recolectar decisiones de secciones
        $decisiones = [
            $request->input('seccion_solicitante'),
            $request->input('seccion_empresa'),
            $request->input('seccion_proyecto'),
            $request->input('seccion_horario'),
            $request->input('seccion_creditos'),
        ];

        // Normalizar: '' -> null
        $decisionesNorm = array_map(function ($v) {
            if ($v === '') return null;
            return $v;
        }, $decisiones);

        // Determinar decisión del encargado
        if (in_array('0', $decisionesNorm, true)) {
            $encValor = 0; // rechazado
        } else {
            $allDecididas = collect($decisionesNorm)->every(fn($v) => $v !== null);
            $allOnes = $allDecididas && collect($decisionesNorm)->every(fn($v) => $v === '1');
            if ($allOnes) {
                $encValor = 2; // encargado aprobó
            } elseif ($allDecididas && collect($decisionesNorm)->every(fn($v) => $v === '1' || $v === null)) {
                $encValor = 1;
            } else {
                $encValor = 1;
            }
        }

        DB::transaction(function () use ($solicitud, $encValor, $request) {
            $autorizacion = AutorizacionSolicitud::updateOrCreate(
                ['Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01],
                [
                    'Autorizo_Empleado' => $encValor,
                    'Comentario_Encargado' => $request->input('comentario_encargado'),
                    'Fecha_As' => now(),
                ]
            );

            if ($encValor === 0) {
                $solicitud->Autorizacion = 0;
                $solicitud->Estado_Encargado = 'rechazado';
                $solicitud->save();

                $this->logBitacora("Encargado RECHAZÓ solicitud");

                EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                    ->whereIn('etapa', [
                        'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                        'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES'
                    ])->update(['estado' => 'pendiente']);
            }

            if ($encValor === 2) {
                $solicitud->Autorizacion = 1;
                $solicitud->Estado_Encargado = 'aprobado';
                $solicitud->save();

                $this->logBitacora("Encargado APROBÓ solicitud");

                EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'realizado']);
            }

            if ($encValor === 1) {
                EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'pendiente']);
            }
        });

        if ($encValor === 0) {
            $solicitud->Autorizacion = 0;
            $solicitud->Estado_Encargado = 'rechazado';
            $solicitud->save();

            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);

            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);

            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES')
                ->update(['estado' => 'pendiente']);
        }
        elseif ($encValor === 2) {
            $solicitud->Autorizacion = 1;
            $solicitud->Estado_Encargado = 'aprobado';
            $solicitud->save();

            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'realizado']);

            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                ->update(['estado' => 'proceso']);

            Expediente::create([
                'Id_Solicitud_FPP01' => $solicitud['Id_Solicitud_FPP01'],
            ]);
        }
        else {
            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);
        }

        return redirect()
            ->route('encargado.solicitudes_alumnos')
            ->with('success', 'Revisión guardada correctamente.');
    }

    // --------------------------------------
    // Para ver los registros de los alumnos
    // --------------------------------------
    public function verRegistros()
    {
        $expedientes = Expediente::with('solicitud.alumno', 'registro')
                                ->whereNotNull('Solicitud_FPP02_Firmada')
                                ->get();

        $carreras = CarreraIngenieria::orderBy('Descripcion_Capitalizadas')->get();

        return view('encargado.registros_alumnos', compact('expedientes', 'carreras'));
    }


    // --------------------------------------------
    // Para calificar los registros de los alumnos
    // --------------------------------------------
    public function calificarRegistro(Request $request)
    {
        $request->validate([
            'seccion' => 'required|string',
            'valor' => 'required|in:0,1',
            'claveAlumno' => 'nullable|string'
        ]);

        $solicitud = SolicitudFPP01::where('Clave_Alumno', $request->claveAlumno)
                                    ->where('Autorizacion', 1)
                                    ->first();

        if (! $solicitud) {
            return abort(404, 'Solicitud no autorizada');
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if ($expediente) {
            $idRegistro = $expediente->Id_Solicitud_FPP02;
            $registro = SolicitudFPP02::where('Id_Solicitud_FPP02', $idRegistro)->first();

            if ($request->valor == 1) {
                $registro->update([
                    'Autorizacion' => 1,
                    'Fecha_Autorizacion' => Carbon::now(),
                ]);
            } else {
                $registro->update([
                    'Autorizacion' => 0,
                ]);
            }

            // ---------------------------------------------
            // SEMAFORIZACIÓN — AUTORIZACIÓN FPP02
            // ---------------------------------------------
            $clave = $request->claveAlumno;

            // ACEPTADO
            if ($request->valor == 1) {

                // Etapa 5: Autorización del Encargado (FPP02) -> VERDE
                EstadoProceso::updateOrCreate(
                    [
                        'clave_alumno' => $clave,
                        'etapa' => 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)'
                    ],
                    [
                        'estado' => 'realizado'
                    ]
                );

                // Etapa 6: Carta de Presentación DSSPP -> AMARILLO
                EstadoProceso::updateOrCreate(
                    [
                        'clave_alumno' => $clave,
                        'etapa' => 'CARTA DE PRESENTACIÓN (DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES)'
                    ],
                    [
                        'estado' => 'proceso'
                    ]
                );
            }

            // RECHAZADO
            else {

                // Etapa 5: Autorización del Encargado (FPP02) -> ROJO
                EstadoProceso::updateOrCreate(
                    [
                        'clave_alumno' => $clave,
                        'etapa' => 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)'
                    ],
                    [
                        'estado' => 'pendiente'
                    ]
                );

                // Etapa 4: Registro FPP02 -> AMARILLO
                EstadoProceso::updateOrCreate(
                    [
                        'clave_alumno' => $clave,
                        'etapa' => 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES'
                    ],
                    [
                        'estado' => 'proceso'
                    ]
                );
            }

            $pdfController = new PDFController();
            $pdfController->generarCartaPresentacion($request->claveAlumno, $expediente);
        }

        return redirect()->route('encargado.registros')->with('success', 'Acción realizada correctamente');
    }

    // -------------------------------------------------
    // Para ver las cartas de aceptación de los alumnos
    // -------------------------------------------------
    public function verAceptacion()
    {
        $expedientes = Expediente::with('solicitud.alumno')
            ->join('solicitud_fpp01', 'expediente.Id_Solicitud_FPP01', '=', 'solicitud_fpp01.Id_Solicitud_FPP01')
            ->whereNotNull('expediente.Carta_Aceptacion')

            // Join para estado
            ->leftJoin('estado_proceso', function($join) {
                $join->on('estado_proceso.clave_alumno', '=', 'solicitud_fpp01.Clave_Alumno')
                    ->where('estado_proceso.etapa', 'CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)');
            })

            // Orden personalizado
            ->orderByRaw("
                FIELD(estado_proceso.estado, 'pendiente', 'proceso', 'realizado') ASC
            ")
            ->orderByDesc('solicitud_fpp01.Fecha_Solicitud')

            ->select('expediente.*')
            ->get();

        $carreras = CarreraIngenieria::orderBy('Descripcion_Capitalizadas')->get();

        return view('encargado.aceptacion_alumnos', compact('expedientes', 'carreras'));
    }

    // -------------------------------------------------------
    // Para calificar las cartas de aceptación de los alumnos
    // -------------------------------------------------------
    public function calificarAceptacion(Request $request)
    {
        $request->validate([
            'seccion' => 'required|string',
            'valor' => 'required|in:0,1',
            'claveAlumno' => 'nullable|string'
        ]);

        $clave = $request->claveAlumno;

        // Obtener solicitud
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $clave)
                                    ->where('Autorizacion', 1)
                                    ->first();

        if (! $solicitud) {
            return abort(404, 'Solicitud no autorizada');
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        // Guardar la decisión del encargado
        if ($expediente) {
            if ($request->valor == 1) {
                $expediente->update([
                    'Autorizacion_Aceptacion' => 1,
                    'Fecha_Autorizacion_Aceptacion' => Carbon::now(),
                ]);
            } else {
                $expediente->update([
                    'Autorizacion_Aceptacion' => 0,
                ]);
            }
        }

        // =============================
        //  SEMAFORIZACIÓN
        // =============================

        if ($request->valor == 1) {

            //  Encargado aprobó
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $clave,
                    'etapa' => 'CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)'
                ],
                ['estado' => 'realizado']
            );

            //  Alumno ya cumplió
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $clave,
                    'etapa' => 'CARTA DE ACEPTACIÓN (ALUMNO)'
                ],
                ['estado' => 'realizado']
            );

            if ($solicitud->Apoyo_Economico == 1) {

                //  SIGUE A: CARTA DE DESGLOSE DE PERCEPCIONES
                EstadoProceso::updateOrCreate(
                    [
                        'clave_alumno' => $clave,
                        'etapa' => 'CARTA DE DESGLOSE DE PERCEPCIONES'
                    ],
                    ['estado' => 'proceso']
                );

            } else {

                //  NO pidió apoyo → Pasa directo al Reporte Parcial
                EstadoProceso::updateOrCreate(
                    [
                        'clave_alumno' => $clave,
                        'etapa' => 'REPORTE PARCIAL'
                    ],
                    ['estado' => 'proceso']
                );

            }

        } else {

            //  Rechazado
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $clave,
                    'etapa' => 'CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)'
                ],
                ['estado' => 'pendiente']
            );

            // 🟡 El alumno puede volver a subir
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $clave,
                    'etapa' => 'CARTA DE ACEPTACIÓN (ALUMNO)'
                ],
                ['estado' => 'proceso']
            );
        }

        return redirect()->route('encargado.cartasAceptacion')->with('success', 'Acción realizada correctamente');
    }

    function normalizarEtapa($texto) {
        return trim(preg_replace('/\(\d+\)/', '', $texto));
    }

    public function calificarCartaPresentacion(Request $request)
    {
        $request->validate([
            'valor' => 'required|in:0,1',
            'claveAlumno' => 'required'
        ]);

        $clave = $request->claveAlumno;

        // Obtener expediente
        $sol = SolicitudFPP01::where('Clave_Alumno', $clave)
            ->where('Autorizacion', 1)
            ->first();

        $exp = Expediente::where('Id_Solicitud_FPP01', $sol->Id_Solicitud_FPP01)->first();

        // Guardar decisión del encargado
        $exp->update([
            'Autorizacion_Presentacion' => $request->valor
        ]);

        // SEMÁFORO
        if ($request->valor == 1) {
            // ACEPTADO
            EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)')
                ->update(['estado' => 'realizado']);

            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $clave,
                    'etapa' => 'CARTA DE PRESENTACIÓN (ALUMNO)'
                ],
                ['estado' => 'proceso']
            );
        }
        else {
            // RECHAZADO
            EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)')
                ->update(['estado' => 'pendiente']);

            EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'CARTA DE PRESENTACIÓN (DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES)')
                ->update(['estado' => 'proceso']);
        }

        return redirect()->route('encargado.cartasPresentacion')
                        ->with('success', 'Acción realizada correctamente.');
    }

    public function revisarCartaPresentacion($claveAlumno)
    {
        $alumno = Alumno::where('Clave_Alumno', $claveAlumno)->firstOrFail();

        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->where('Autorizacion', 1)
            ->firstOrFail();

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->firstOrFail();

        if (!$expediente->Carta_Presentacion) {
            abort(404, "No existe carta generada");
        }

        $pdfPath = 'storage/expedientes/Carta_Presentacion/' . $expediente->Carta_Presentacion;

        return view('encargado.revisar_presentacion', [
            'alumno' => $alumno,
            'claveAlumno' => $claveAlumno,
            'pdfPath' => $pdfPath
        ]);
    }

    public function accionCartaPresentacion(Request $request)
    {
        $clave = $request->claveAlumno;
        $accion = $request->accion; // aprobar | rechazar

        $sol = SolicitudFPP01::where('Clave_Alumno', $clave)
            ->where('Autorizacion', 1)
            ->first();

        $exp = Expediente::where('Id_Solicitud_FPP01', $sol->Id_Solicitud_FPP01)->first();

        if ($accion === 'aprobar') {

            $exp->update([
                'Autorizacion_Presentacion' => 1,
                'Fecha_Autorizacion_Presentacion' => now(),
            ]);

            // Semaforización
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $clave,
                    'etapa' => 'CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)'
                ],
                ['estado' => 'realizado']
            );

            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $clave,
                    'etapa' => 'CARTA DE PRESENTACIÓN (ALUMNO)'
                ],
                ['estado' => 'proceso']
            );

            return back()->with('success','Carta aprobada correctamente.');
        }

        // Si rechazó
        $exp->update([
            'Autorizacion_Presentacion' => 0,
        ]);

        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $clave,
                'etapa' => 'CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)'
            ],
            ['estado' => 'pendiente']
        );

        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $clave,
                'etapa' => 'CARTA DE PRESENTACIÓN (DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES)'
            ],
            ['estado' => 'proceso']
        );

        return back()->with('success','Carta rechazada correctamente.');
    }

    public function cartasPresentacionEncargado()
    {
        $expedientes = Expediente::with('solicitud.alumno')
            ->whereNotNull('Carta_Presentacion')
            ->join('solicitud_fpp01', 'expediente.Id_Solicitud_FPP01', '=', 'solicitud_fpp01.Id_Solicitud_FPP01')

            // ORDEN PERSONALIZADO
            ->leftJoin('estado_proceso', function($join) {
                $join->on('estado_proceso.clave_alumno', '=', 'solicitud_fpp01.Clave_Alumno')
                    ->where('estado_proceso.etapa', 'CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)');
            })

            // 1️⃣ Primero estado pendiente, luego proceso, luego realizado
            ->orderByRaw("
                FIELD(estado_proceso.estado, 'pendiente', 'proceso', 'realizado') ASC
            ")

            // 2️⃣ Luego por fecha (más nuevas arriba)
            ->orderByDesc('solicitud_fpp01.Fecha_Solicitud')

            ->select('expediente.*')
            ->get();

        $alumnos = Alumno::all();
        return view('encargado.cartas_presentacion', compact('expedientes', 'alumnos'));

    }

    // LISTA DE ALUMNOS PARA LIBERAR
    public function listaLiberacion()
    {
        $alumnos = EstadoProceso::where('etapa', 'LIBERACIÓN DEL ALUMNO')
            ->where('estado', 'proceso')
            ->join('alumno', 'alumno.Clave_Alumno', '=', 'estado_proceso.clave_alumno')
            ->select(
                'alumno.Clave_Alumno as clave',
                DB::raw("CONCAT(alumno.Nombre, ' ', alumno.ApellidoP_Alumno, ' ', alumno.ApellidoM_Alumno) AS nombre"),
                'alumno.Carrera as carrera'
            )
            ->get();

        return view('encargado.liberacion', compact('alumnos'));
    }

    // APROBAR LIBERACIÓN
    public function aprobarLiberacion($clave)
    {
        EstadoProceso::where('clave_alumno', $clave)
            ->where('etapa', 'LIBERACIÓN DEL ALUMNO')
            ->update([
                'estado' => 'realizado',
                'Fecha_Termino' => now()
            ]);

        EstadoProceso::where('clave_alumno', $clave)
            ->where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
            ->update(['estado' => 'proceso']);

        return back()->with('success', 'Alumno liberado correctamente.');
    }

    // RECHAZAR
    public function rechazarLiberacion($clave)
    {
        // 1. LIBERACIÓN DEL ALUMNO → pendiente (rojo)
        EstadoProceso::where('clave_alumno', $clave)
            ->where('etapa', 'LIBERACIÓN DEL ALUMNO')
            ->update([
                'estado' => 'pendiente'
            ]);

        // 2. CALIFICACIÓN FINAL → proceso (amarillo)
        EstadoProceso::where('clave_alumno', $clave)
            ->where('etapa', 'CALIFICACIÓN FINAL')
            ->update([
                'estado' => 'proceso'
            ]);

        return back()->with('reject', 'Liberación rechazada.');
    }


}
