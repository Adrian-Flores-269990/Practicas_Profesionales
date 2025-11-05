<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\EstadoProceso;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudFPP02;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AlumnoController extends Controller
{
    public function create()
    {
        return view('alumno.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Clave_Alumno' => 'required|integer|unique:alumno,Clave_Alumno',
            'Nombre' => 'required|string|max:100',
            'CorreoElectronico' => 'nullable|email|max:150'
        ]);

        $alumno = Alumno::create($validated + [
            'ApellidoP_Alumno' => $request->ApellidoP_Alumno,
            'ApellidoM_Alumno' => $request->ApellidoM_Alumno,
            'Semestre' => $request->Semestre,
            'Carrera' => $request->Carrera,
            'TelefonoCelular' => $request->TelefonoCelular,
            'Clave_Materia' => $request->Clave_Materia,
            'Clave_Carrera' => $request->Clave_Carrera,
            'Clave_Area' => $request->Clave_Area,
        ]);

        // Inicializar todas las etapas del proceso con estado 'pendiente'
        $etapas = [
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
            'REPORTE PARCIAL NO. X',
            'REVISIÓN REPORTE PARCIAL NO. X',
            'CORRECCIÓN REPORTE PARCIAL NO. X',
            'REPORTE FINAL',
            'REVISIÓN REPORTE FINAL',
            'CORRECCIÓN REPORTE FINAL',
            'CALIFICACIÓN REPORTE FINAL',
            'CARTA DE TÉRMINO',
            'EVALUACIÓN DE LA EMPRESA',
            'CALIFICACIÓN FINAL',
            'EVALUACIÓN DEL ALUMNO',
            'LIBERACIÓN DEL ALUMNO',
            'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES',
            'DOCUMENTO EXTRA (EJEMPLO)',
        ];

        foreach ($etapas as $etapa) {
            EstadoProceso::firstOrCreate([
                'clave_alumno' => $alumno->Clave_Alumno,
                'etapa' => $etapa
            ], [
                'estado' => 'pendiente'
            ]);
        }

        return back()->with('success', 'Alumno insertado correctamente en la base de datos.');
    }

    public function estadoAlumno()
    {
        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? null;

        if (!$claveAlumno) {
            return redirect()->route('alumno.inicio')
                ->with('error', 'No se encontró la clave del alumno en la sesión.');
        }

        // Buscar solicitud actual
        $solicitud = \App\Models\SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        if (!$solicitud) {
            $procesos = \App\Models\EstadoProceso::where('clave_alumno', $claveAlumno)->get();
            return view('alumno.estado', compact('procesos'));
        }

        // Estados actuales
        $estadoDepto = $solicitud->Estado_Departamento;
        $estadoEncargado = $solicitud->Estado_Encargado;

        // Determinar si alguno rechazó
        $reiniciar = ($estadoDepto === 'rechazado' || $estadoEncargado === 'rechazado');

        // Etapas principales
        $procesos = [
            [
                'etapa' => 'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                'estado' => $reiniciar ? 'proceso' : 'realizado',
            ],
            [
                'etapa' => 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                'estado' => match (true) {
                    //  Si el encargado rechazó, se fuerza a pendiente
                    $estadoEncargado === 'rechazado' => 'pendiente',
                    //  Si el DSSPP aprobó y el encargado NO rechazó
                    $estadoDepto === 'aprobado' && $estadoEncargado !== 'rechazado' => 'realizado',
                    //  Si el DSSPP rechazó
                    $estadoDepto === 'rechazado' => 'pendiente',
                    //  En cualquier otro caso, sigue en proceso
                    default => 'proceso',
                },
            ],
            [
                'etapa' => 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                'estado' => match ($estadoEncargado) {
                    'aprobado' => 'realizado',
                    'rechazado' => 'pendiente',
                    default => (
                        $estadoDepto === 'aprobado' ? 'proceso' : 'pendiente'
                    ),
                },
            ],
            [
                'etapa' => 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                // Solo pasa a “proceso” si ambos aprobaron
                'estado' => (
                    $estadoEncargado === 'aprobado' && $estadoDepto === 'aprobado'
                )
                    ? 'proceso'
                    : 'pendiente',
            ],
        ];

        // Actualizar o crear las 4 primeras etapas
        foreach ($procesos as $p) {
            \App\Models\EstadoProceso::updateOrCreate(
                ['clave_alumno' => $claveAlumno, 'etapa' => $p['etapa']],
                ['estado' => $p['estado']]
            );
        }

        $procesos = \App\Models\EstadoProceso::where('clave_alumno', $claveAlumno)->get();
        return view('alumno.estado', compact('procesos'));
    }

    public function aceptar(Request $request)
    {
        $alumno = session('alumno');
        $clave = $alumno['cve_uaslp'] ?? null;

        if (!$alumno || !$clave) {
            return redirect()->route('alumno.inicio')
                ->with('error', 'No se encontró la sesión del alumno.');
        }

        $solicitud = \App\Models\SolicitudFPP01::where('Clave_Alumno', $clave)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        if (!$solicitud) {
            return back()->with('error', 'No se encontró la solicitud FPP01 previa.');
        }

        // =====================
        // CÁLCULOS AUTOMÁTICOS
        // =====================
        $fechaInicio = Carbon::parse($solicitud->Fecha_Inicio);
        $fechaTermino = Carbon::parse($solicitud->Fecha_Termino);
        $numMeses = $fechaInicio->diffInMonths($fechaTermino);

        $horarioEntrada = Carbon::parse($solicitud->Horario_Entrada);
        $horarioSalida = Carbon::parse($solicitud->Horario_Salida);
        $horasPorDia = abs($horarioSalida->diffInHours($horarioEntrada));

        $diasSemana = 0;
        foreach (['L', 'M', 'X', 'J', 'V', 'S'] as $dia) {
            if (!empty($solicitud->$dia) && $solicitud->$dia == 1) $diasSemana++;
        }
        $totalHoras = max(0, $horasPorDia * ($diasSemana ?: 5) * $numMeses * 4);

        // =====================
        // INSERTAR EN FPP02
        // =====================
        SolicitudFPP02::create([
            'Asignacion_Oficial_DSSPP' => 0,
            'Fecha_Asignacion' => now(),
            'Servicio_Social' => 0,
            'Num_Meses' => $numMeses,
            'Total_Horas' => $totalHoras,
            'Autorizacion' => 1,
            'Fecha_Autorizacion' => now(),
        ]);

        // =====================
        // ACTUALIZAR SEMÁFORO
        // =====================
        try {
            $updated = EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $clave,
                    'etapa' => 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                ],
                [
                    'estado' => 'realizado',
                ]
            );

            Log::info('EstadoProceso actualizado correctamente al imprimir', [
                'clave_alumno' => $clave,
                'etapa' => $updated->etapa,
                'nuevo_estado' => $updated->estado
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar EstadoProceso al imprimir: ' . $e->getMessage());
        }

        // =====================
        // GENERAR PDF
        // =====================
        $pdf = PDF::loadView('pdf.fpp02', compact('alumno', 'solicitud'))
            ->setPaper('letter', 'portrait');

        session(['fpp02_impreso' => true]);

        return $pdf->download('Formato_FPP02.pdf');
    }

    public function confirmaFPP02()
    {
        $alumno = session('alumno');
        $clave = $alumno['cve_uaslp'] ?? null;

        if (!$clave) {
            return redirect()->route('alumno.inicio')
                ->with('error', 'No se encontró la clave del alumno en la sesión.');
        }

        $solicitud = \App\Models\SolicitudFPP01::with([
            'alumno',
            'dependenciaMercadoSolicitud.dependenciaEmpresa',
            'dependenciaMercadoSolicitud.sectorPrivado',
            'dependenciaMercadoSolicitud.sectorPublico',
            'dependenciaMercadoSolicitud.sectorUaslp',
            'autorizaciones'
        ])
        ->where('Clave_Alumno', $clave)
        ->latest('Id_Solicitud_FPP01')
        ->first();

        if (!$solicitud) {
            return redirect()->route('alumno.estado')
                ->with('error', 'No se encontró ninguna solicitud previa.');
        }

        $dependencia = $solicitud->dependenciaMercadoSolicitud;
        $empresa = optional($dependencia)->dependenciaEmpresa;

        $sector = null;
        $tipoSector = null;
        if ($dependencia?->Id_Privado) {
            $sector = $dependencia->sectorPrivado;
            $tipoSector = 'privado';
        } elseif ($dependencia?->Id_Publico) {
            $sector = $dependencia->sectorPublico;
            $tipoSector = 'publico';
        } elseif ($dependencia?->Id_UASLP) {
            $sector = $dependencia->sectorUaslp;
            $tipoSector = 'uaslp';
        }

        // Determinar si debe mostrar upload
        $registroFpp02 = EstadoProceso::where('clave_alumno', $clave)
            ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
            ->first();

        $mostrarUpload =
            session('fpp02_impreso_' . $clave, false) ||
            ($registroFpp02 && $registroFpp02->estado === 'realizado');

        return view('alumno.registro', compact('alumno', 'solicitud', 'empresa', 'sector', 'tipoSector', 'mostrarUpload'));
    }
}
