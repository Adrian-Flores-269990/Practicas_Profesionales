<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SolicitudFPP01;
use App\Models\AutorizacionSolicitud;
use App\Models\EstadoProceso;
use App\Models\CarreraIngenieria;
use App\Models\Alumno; // Asegúrate de tener este modelo
use App\Services\UaslpApiService;

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
        
        // Si hay búsqueda
        if ($request->has('busqueda') && !empty($request->busqueda)) {
            $busqueda = trim($request->busqueda);
            
            // Buscar SOLO alumnos que tienen al menos una solicitud
            $alumnosQuery = Alumno::whereHas('solicitudes') // Solo los que tienen solicitudes
                ->where(function($query) use ($busqueda) {
                    $query->where('Clave_Alumno', 'LIKE', "%{$busqueda}%")
                        ->orWhere('ApellidoP_Alumno', 'LIKE', "%{$busqueda}%")
                        ->orWhere('ApellidoM_Alumno', 'LIKE', "%{$busqueda}%")
                        ->orWhere(DB::raw("CONCAT(ApellidoP_Alumno, ' ', ApellidoM_Alumno)"), 'LIKE', "%{$busqueda}%")
                        ->orWhere(DB::raw("CONCAT(Nombre, ' ', ApellidoP_Alumno, ' ', ApellidoM_Alumno)"), 'LIKE', "%{$busqueda}%");
                })
                ->with('solicitudes') // Cargar sus solicitudes
                ->limit(10)
                ->get();

            // Mapear a formato esperado por la vista
            $alumnos = $alumnosQuery->map(function($alumno) {
                return [
                    'cve_uaslp' => $alumno->Clave_Alumno,
                    'nombres' => $alumno->Nombre,
                    'paterno' => $alumno->ApellidoP_Alumno,
                    'materno' => $alumno->ApellidoM_Alumno,
                    'carrera' => $alumno->Carrera,
                    'semestre' => $alumno->Semestre,
                    'creditos' => $alumno->Creditos,
                    'correo' => $alumno->CorreoElectronico,
                ];
            })->toArray();
        }
        
        return view('encargado.consultar_alumno', compact('alumnos'));
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

}