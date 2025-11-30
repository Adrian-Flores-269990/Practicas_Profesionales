<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\EstadoProceso;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudFPP02;
use Carbon\Carbon;
use App\Models\Reporte;
use App\Models\Expediente;
use Illuminate\Support\Facades\DB;

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
            'REPORTE PARCIAL',
            'REVISIÓN REPORTE PARCIAL',
            'CORRECCIÓN REPORTE PARCIAL',
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
        //dd(session('alumno'));
        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? $alumno['Clave_Alumno'] ?? null;

        if (!$claveAlumno) {
            return redirect()->route('alumno.inicio')
                ->with('error', 'No se encontró la clave del alumno en la sesión.');
        }

        // Buscar solicitud FPP01 más reciente
        $solicitud = \App\Models\SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        // Si nunca ha hecho FPP01 → solo mostrar semáforo inicial
        if (!$solicitud) {
            $procesos = \App\Models\EstadoProceso::where('clave_alumno', $claveAlumno)->get();
            return view('alumno.estado', compact('procesos'));
        }

        // Estados de FPP01
        $estadoDepto = $solicitud->Estado_Departamento;    // etapa 2
        $estadoEncargado = $solicitud->Estado_Encargado;  // etapa 3

        // Determinar si hubo rechazo
        $reiniciar = ($estadoDepto === 'rechazado' || $estadoEncargado === 'rechazado');

        // ============================
        //      ETAPA 1 – REGISTRO
        // ============================
        $etapa1 = $reiniciar ? 'proceso' : 'realizado';

        // ============================
        //      ETAPA 2 – DSSPP (FPP01)
        // ============================
        $etapa2 = match (true) {
            $estadoEncargado === 'rechazado' => 'pendiente', // si encargado rechazó, reinicia
            $estadoDepto === 'aprobado' => 'realizado',      // DSSPP aprobó
            $estadoDepto === 'rechazado' => 'pendiente',
            default => 'proceso',                            // en revisión
        };

        // ============================
        //      ETAPA 3 – Encargado (FPP01)
        // ============================
        $etapa3 = match ($estadoEncargado) {
            'aprobado' => 'realizado',
            'rechazado' => 'pendiente',
            default => ($estadoDepto === 'aprobado' ? 'proceso' : 'pendiente'),
        };

        // ============================
        //      ETAPA 4 – REGISTRO FPP02
        // ============================
        // NO sobreescribir si ya está en proceso o realizado
        $etapa4Real = EstadoProceso::where('clave_alumno', $claveAlumno)
            ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
            ->value('estado');

        $etapa4 = $etapa4Real ?? 'pendiente';

        // ============================
        //  GUARDAR SOLO ETAPAS 1, 2 Y 3
        // ============================
        $procesosToUpdate = [
            [
                'etapa'  => 'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                'estado' => $etapa1,
            ],
            [
                'etapa'  => 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                'estado' => $etapa2,
            ],
            [
                'etapa'  => 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                'estado' => $etapa3,
            ],
            // Etapa 4 SOLO se muestra, NO se sobreescribe
        ];

        foreach ($procesosToUpdate as $p) {
            EstadoProceso::updateOrCreate(
                ['clave_alumno' => $claveAlumno, 'etapa' => $p['etapa']],
                ['estado' => $p['estado']]
            );
        }

        // Obtener todas las etapas reales del alumno
        $procesos = EstadoProceso::where('clave_alumno', $claveAlumno)->get();

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
            'CALIFICACIÓN REPORTE FINAL',
            'CARTA DE TÉRMINO',
            'EVALUACIÓN DE LA EMPRESA',
            'CALIFICACIÓN FINAL',
            'EVALUACIÓN DEL ALUMNO',
            'LIBERACIÓN DEL ALUMNO',
            'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES',
            'DOCUMENTO EXTRA (EJEMPLO)'
        ];

        $procesos = $procesos->sortBy(function ($item) use ($ordenEtapas) {
            return array_search($item->etapa, $ordenEtapas);
        })->values();

        // ===============================
        //  CALCULAR ÚLTIMO REPORTE PARCIAL REAL
        // ===============================
        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        $ultimoParcialReal = 0;

        if ($expediente) {
            $ultimoParcialReal = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
                ->where('Reporte_Final', 0) // SOLO los parciales reales
                ->max('Numero_Reporte') ?? 0;
        }

        // ===============================
        //  REEMPLAZAR NOMBRES DE ETAPAS PARCIALES
        // ===============================
        foreach ($procesos as $p) {

            $original = $p->etapa;
            $nuevo = $original;

            // REPORTE PARCIAL
            if (
                str_contains($original, 'REPORTE PARCIAL') &&
                !str_contains($original, 'REVISIÓN') &&
                !str_contains($original, 'CORRECCIÓN')
            ) {
                $nuevo = "REPORTE PARCIAL ($ultimoParcialReal)";
            }

            // REVISIÓN REPORTE PARCIAL
            if (str_contains($original, 'REVISIÓN REPORTE PARCIAL')) {
                $nuevo = "REVISIÓN REPORTE PARCIAL ($ultimoParcialReal)";
            }

            // CORRECCIÓN REPORTE PARCIAL
            if (str_contains($original, 'CORRECCIÓN REPORTE PARCIAL')) {
                $nuevo = "CORRECCIÓN REPORTE PARCIAL ($ultimoParcialReal)";
            }

            // Guardar versión final para el Blade
            $p->etapa = $nuevo;
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        $reportesAprobados = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->whereNotNull('Calificacion')
            ->where('Calificacion', '>=', 60)
            ->count();

        $reportesExistentes = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->orderBy('Numero_Reporte')
            ->get();

        $todosAprobados = $this->todosReportesAprobados($expediente);

        return view('alumno.estado', compact('procesos', 'reportesExistentes', 'reportesAprobados', 'todosAprobados'));

    }

    protected function todosReportesAprobados($expediente)
    {
        $carreraAlumno = $expediente->solicitudFPP01->alumno->Carrera ?? null;
        if (!$carreraAlumno) return false;

        $reportesRaw = DB::table('requisito_carrera as rc')
            ->join('carrera_ingenieria as ci', 'rc.Id_Carrera_I', '=', 'ci.Id_Carrera_I')
            ->where('ci.Descripcion_Mayúsculas', $carreraAlumno)
            ->distinct()
            ->pluck('rc.num_reporte');

        $allowedReportes = collect();
        if ($reportesRaw->isNotEmpty()) {
            $numbers = $reportesRaw->flatMap(function($v) {
                $v = trim((string)$v);
                if ($v === '') return collect();
                if (strpos($v, ',') !== false) return collect(array_map('intval', explode(',', $v)));
                if (strpos($v, '-') !== false) {
                    [$a, $b] = array_map('intval', explode('-', $v));
                    return collect(range(min($a,$b), max($a,$b)));
                }
                return collect([(int)$v]);
            })->filter(fn($n)=>$n>0)->unique()->sort()->values();

            $allowedReportes = ($numbers->count() === 1 && $numbers[0] > 1) ? collect(range(1,$numbers[0])) : $numbers;
        }

        $reportesAprobados = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->whereIn('Numero_Reporte', $allowedReportes)
            ->where('Calificacion','>=',60)
            ->pluck('Numero_Reporte');

        return $allowedReportes->diff($reportesAprobados)->isEmpty();
    }

    public function guardarMateria(Request $request)
    {
        $alumno = session('alumno');

        if (!$alumno) {
            return redirect()->route('alumno.inicio')->with('error', 'Sesión no válida.');
        }

        // Nivel: PP I o PP II
        $nivel = $request->get('nivel');  // <----- AQUI EL ARREGLO

        if (!$nivel) {
            return redirect()->route('alumno.inicio')->with('error', 'No se envió el nivel.');
        }

        // Buscar carrera en BD
        $carreraBD = \App\Models\CarreraIngenieria::where(
            'Descripcion_Capitalizadas',
            trim($alumno['carrera'])
        )->first();

        if (!$carreraBD) {
            return back()->with('error', 'Tu carrera no está registrada en la BD.');
        }

        $idCarrera = $carreraBD->Id_Carrera_I;

        $materias = \App\Models\RequisitoCarrera::where('Id_Carrera_I', $idCarrera)->get();

        if ($materias->isEmpty()) {
            return back()->with('error', 'No hay materias para tu carrera.');
        }

        // REGRA #1: PP I
        if ($nivel == 1) {
            $materia = $materias->first(function ($m) {
                $n = trim($m->Espacio_de_Formacion);
                if (str_ends_with($n, ' II')) return false;
                if (str_ends_with($n, ' I')) return true;
                return true;
            });
        }

        // REGLA #2: PP II
        if ($nivel == 2) {
            $materia = $materias->first(function ($m) {
                return str_ends_with(trim($m->Espacio_de_Formacion), ' II');
            });
        }

        if (!$materia) {
            return back()->with('error', 'No existe materia válida para este nivel.');
        }

        session(['materia_practicas' => $materia->Espacio_de_Formacion]);

        return redirect()->route('alumno.estado');
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
        // GENERAR PDF
        // =====================
        $pdf = PDF::loadView('pdf.fpp02', compact('alumno', 'solicitud'))
            ->setPaper('letter', 'portrait');

        session(['fpp02_impreso' => true]);

        return $pdf->download('Formato_FPP02.pdf');
    }
}
