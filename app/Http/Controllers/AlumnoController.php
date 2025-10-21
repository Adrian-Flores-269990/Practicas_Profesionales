<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\EstadoProceso;

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

        // 2️⃣ Inicializar todas las etapas del proceso con estado 'pendiente'
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
        ->latest('Id_Solicitud_FPP01') // Usa la solicitud más reciente
        ->first();

        if (!$solicitud) {
            $procesos = \App\Models\EstadoProceso::where('clave_alumno', $claveAlumno)->get();
            return view('alumno.estado', compact('procesos'));
        }

        // --- Estados dinámicos según la revisión de DSSPP y Encargado ---
        $estadoDepto = $solicitud->Estado_Departamento;
        $estadoEncargado = $solicitud->Estado_Encargado;

        // Si alguno rechazó, se reinicia a “proceso”
        $reiniciar = ($estadoDepto === 'rechazado' || $estadoEncargado === 'rechazado');

        $procesos = [
            [
                'etapa' => 'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                'estado' => $reiniciar ? 'proceso' : 'realizado',
            ],
            [
                'etapa' => 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                'estado' => match ($estadoDepto) {
                    'aprobado' => 'realizado',
                    'rechazado' => 'pendiente',
                    default => 'proceso',
                },
            ],
            [
                'etapa' => 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                'estado' => match ($estadoEncargado) {
                    'aprobado' => 'realizado',
                    'rechazado' => 'pendiente',
                    default => (
                        $estadoDepto == 'aprobado' ? 'proceso' : 'pendiente'
                    ),
                },
            ],
            [
                'etapa' => 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                'estado' => (
                    $estadoEncargado === 'aprobado' && $estadoDepto === 'aprobado'
                )
                    ? 'proceso' // ambos aprobaron → avanza
                    : ($reiniciar ? 'proceso' : 'pendiente'), // si alguno rechazó → reinicia
            ],
        ];

        // 🔄 Actualizar o crear las 4 primeras etapas
        foreach ($procesos as $p) {
            \App\Models\EstadoProceso::updateOrCreate(
                ['clave_alumno' => $claveAlumno, 'etapa' => $p['etapa']],
                ['estado' => $p['estado']]
            );
        }

        $procesos = \App\Models\EstadoProceso::where('clave_alumno', $claveAlumno)->get();
        return view('alumno.estado', compact('procesos'));
    }


}
