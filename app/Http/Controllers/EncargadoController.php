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
use Carbon\Carbon;

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
                }

                // Obtener estados de proceso (semáforo) del alumno
                $semaforo = [];
                if (isset($estadosProceso[$alumno->Clave_Alumno])) {
                    $semaforo = $estadosProceso[$alumno->Clave_Alumno]->map(function($estado) {
                        return [
                            'etapa' => $estado->etapa,
                            'estado' => $estado->estado,
                        ];
                    })->toArray();
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

    public function verRegistros()
    {
        $expedientes = Expediente::with('solicitud.alumno', 'registro')
                                ->whereNotNull('Solicitud_FPP02_Firmada')
                                ->get();

        $carreras = CarreraIngenieria::orderBy('Descripcion_Capitalizadas')->get();

        return view('encargado.registros_alumnos', compact('expedientes', 'carreras'));
    }



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

            $pdfController = new PDFController();
            $pdfController->generarCartaPresentacion($request->claveAlumno, $expediente);
        }

        return redirect()->route('encargado.registros')->with('success', 'Acción realizada correctamente');
    }

}
