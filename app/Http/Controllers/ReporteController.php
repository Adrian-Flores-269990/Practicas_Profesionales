<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;
use App\Models\Expediente;
use App\Models\SolicitudFPP01;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Mostrar la vista para crear un nuevo reporte
     */
    public function create()
    {
        $alumno = session('alumno');
        if (!$alumno) {
            return redirect()->route('alumno.login')->withErrors(['error' => 'Sesión expirada']);
        }

        $claveAlumno = $alumno['cve_uaslp'] ?? null;
        if (!$claveAlumno) {
            return redirect()->route('alumno.inicio')->withErrors(['error' => 'No se encontró la clave del alumno']);
        }

        // Obtener solicitud
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        if (!$solicitud) {
            return redirect()->route('alumno.inicio')->withErrors(['error' => 'No se encontró solicitud FPP01']);
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (!$expediente) {
            return redirect()->route('alumno.inicio')->withErrors(['error' => 'No se encontró expediente']);
        }

        // obtener la carrera del alumno
        $carreraAlumno = $solicitud->alumno->Carrera ?? null;

        $allowedReportes = null;

        if ($carreraAlumno) {
            $reportesRaw = DB::table('requisito_carrera as rc')
                ->join('carrera_ingenieria as ci', 'rc.Id_Carrera_I', '=', 'ci.Id_Carrera_I')
                ->where('ci.Descripcion_Mayúsculas', $carreraAlumno)
                ->distinct()
                ->pluck('rc.num_reporte');

            if ($reportesRaw->isNotEmpty()) {
                // Normalizar y expandir
                $numbers = $reportesRaw->flatMap(function($v) {
                    $v = trim((string)$v);
                    if ($v === '') return collect();

                    if (strpos($v, ',') !== false) {
                        $parts = array_map('trim', explode(',', $v));
                        return collect($parts);
                    }

                    if (strpos($v, '-') !== false) {
                        [$a, $b] = array_map('intval', explode('-', $v));
                        return collect(range(min($a, $b), max($a, $b)));
                    }

                    return collect([$v]);
                })->map(fn($x) => intval($x))
                  ->filter(fn($n) => $n > 0)
                  ->unique()
                  ->sort()
                  ->values();

                if ($numbers->count() === 1 && $numbers[0] > 1) {
                    $allowedReportes = collect(range(1, $numbers[0]));
                } else {
                    $allowedReportes = $numbers;
                }
            }
        }

        // Obtener reportes existentes
        $reportesExistentes = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->orderBy('Numero_Reporte')
            ->get();

        $todosAprobados = $this->todosReportesAprobados($expediente);

        return view('alumno.reporte', compact('alumno', 'expediente', 'reportesExistentes', 'allowedReportes', 'todosAprobados'));
    }

    /**
     * Almacenar un nuevo reporte
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_reporte' => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'resumen' => 'required|string|max:5000',
            'reporte_final' => 'nullable|boolean',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        // Verificar sesión
        $alumno = session('alumno');
        if (!$alumno) {
            return response()->json(['error' => 'Sesión expirada'], 401);
        }

        $claveAlumno = $alumno['cve_uaslp'] ?? null;

        // Obtener expediente
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        if (!$solicitud) {
            return response()->json(['error' => 'No se encontró solicitud FPP01'], 404);
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (!$expediente) {
            return response()->json(['error' => 'No se encontró expediente'], 404);
        }

        if ($request->numero_reporte == 100) {
            // Buscar si ya existe reporte final
            $reporteExistente = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
                ->where('Reporte_Final', 1)
                ->first();

            // =========================================
            // CASO 1: EXISTE → evaluar si puedo sobrescribir
            // =========================================
            if ($reporteExistente) {

                // Si ya está aprobado, NO permitir sobrescribir
                if ($reporteExistente->Calificacion !== null && $reporteExistente->Calificacion >= 60) {
                    return response()->json([
                        'error' => "El reporte final ya fue aprobado y no puede modificarse."
                    ], 422);
                }

                // Si está reprobado o sin calificar → PERMITIR sobrescribir
                DB::beginTransaction();
                try {
                    $nombreArchivo = $reporteExistente->Nombre_Archivo;

                    if ($request->hasFile('archivo_pdf')) {
                        $archivo = $request->file('archivo_pdf');
                        $timestamp = now()->format('YmdHis');
                        $nombreArchivo = "{$claveAlumno}_reporte_final_{$timestamp}.pdf";

                        if ($reporteExistente->Nombre_Archivo &&
                            Storage::disk('public')->exists("expedientes/reportes/" . $reporteExistente->Nombre_Archivo)) {
                            Storage::disk('public')->delete("expedientes/reportes/" . $reporteExistente->Nombre_Archivo);
                        }

                        $archivo->storeAs("expedientes/reportes", $nombreArchivo, 'public');
                    }

                    $reporteExistente->update([
                        'Periodo_Ini' => $request->fecha_inicio,
                        'Periodo_Fin' => $request->fecha_fin,
                        'Resumen_Actividad' => $request->resumen,
                        'Reporte_Final' => 1,
                        'Archivo_Agregado' => $request->hasFile('archivo_pdf') ? 1 : $reporteExistente->Archivo_Agregado,
                        'Calificacion' => null,
                        'Observaciones' => null,
                        'Nombre_Archivo' => $nombreArchivo,
                    ]);

                    // Actualizar semaforización
                    DB::table('estado_proceso')->updateOrInsert(
                        ['clave_alumno' => $claveAlumno, 'etapa' => 'REPORTE FINAL'],
                        ['estado' => 'realizado']
                    );
                    DB::table('estado_proceso')->updateOrInsert(
                        ['clave_alumno' => $claveAlumno, 'etapa' => 'REVISIÓN REPORTE FINAL'],
                        ['estado' => 'proceso']
                    );
                    DB::table('estado_proceso')->updateOrInsert(
                        ['clave_alumno' => $claveAlumno, 'etapa' => 'CORRECCIÓN REPORTE FINAL'],
                        ['estado' => 'pendiente']
                    );

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => "Reporte final actualizado (sobrescrito) correctamente",
                        'reporte' => $reporteExistente
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    if ($nombreArchivo && Storage::disk('public')->exists("expedientes/reportes/{$nombreArchivo}")) {
                        Storage::disk('public')->delete("expedientes/reportes/{$nombreArchivo}");
                    }
                    return response()->json(['error' => "Error al actualizar reporte final: " . $e->getMessage()], 500);
                }
            }

            // =========================================
            // CASO 2: NO EXISTE → crear nuevo reporte final
            // =========================================
            DB::beginTransaction();
            try {
                $nombreArchivo = null;
                $archivoAgregado = 0;

                if ($request->hasFile('archivo_pdf')) {
                    $archivo = $request->file('archivo_pdf');
                    $timestamp = now()->format('YmdHis');
                    $nombreArchivo = "{$claveAlumno}_reporte_final_{$timestamp}.pdf";
                    $archivo->storeAs("expedientes/reportes", $nombreArchivo, 'public');
                    $archivoAgregado = 1;
                }

                $reporteFinal = Reporte::create([
                    'Id_Expediente' => $expediente->Id_Expediente,
                    'Numero_Reporte' => 100,
                    'Periodo_Ini' => $request->fecha_inicio,
                    'Periodo_Fin' => $request->fecha_fin,
                    'Resumen_Actividad' => $request->resumen,
                    'Reporte_Final' => 1,
                    'Archivo_Agregado' => $archivoAgregado,
                    'Calificacion' => null,
                    'Observaciones' => null,
                    'Nombre_Archivo' => $nombreArchivo,
                ]);

                // Semaforización
                DB::table('estado_proceso')->updateOrInsert(
                    ['clave_alumno' => $claveAlumno, 'etapa' => 'REPORTE FINAL'],
                    ['estado' => 'realizado']
                );
                DB::table('estado_proceso')->updateOrInsert(
                    ['clave_alumno' => $claveAlumno, 'etapa' => 'REVISIÓN REPORTE FINAL'],
                    ['estado' => 'proceso']
                );
                DB::table('estado_proceso')->updateOrInsert(
                    ['clave_alumno' => $claveAlumno, 'etapa' => 'CORRECCIÓN REPORTE FINAL'],
                    ['estado' => 'pendiente']
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Reporte final enviado correctamente',
                    'reporte' => $reporteFinal
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                if ($nombreArchivo && Storage::disk('public')->exists("expedientes/reportes/{$nombreArchivo}")) {
                    Storage::disk('public')->delete("expedientes/reportes/{$nombreArchivo}");
                }
                return response()->json(['error' => "Error al guardar reporte final: " . $e->getMessage()], 500);
            }
        }
        // Buscar reporte existente por número
        $reporteExistente = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->where('Numero_Reporte', $request->numero_reporte)
            ->first();

        // =========================================
        // CASO 1: EXISTE → evaluar si puedo sobrescribir
        // =========================================
        if ($reporteExistente) {

            // Si ya está aprobado, NO permitir sobrescribir
            if ($reporteExistente->Calificacion !== null && $reporteExistente->Calificacion >= 60) {
                return response()->json([
                    'error' => "El reporte número {$request->numero_reporte} ya fue aprobado y no puede modificarse."
                ], 422);
            }

            // Si está reprobado o sin calificar → PERMITIR sobrescribir
            DB::beginTransaction();
            try {
                $nombreArchivo = $reporteExistente->Nombre_Archivo;

                // Si envía un PDF nuevo → sobrescribir
                if ($request->hasFile('archivo_pdf')) {
                    $archivo = $request->file('archivo_pdf');
                    $timestamp = now()->format('YmdHis');
                    $nombreArchivo = "{$claveAlumno}_reporte_{$request->numero_reporte}_{$timestamp}.pdf";

                    // Borrar archivo anterior si existía
                    if ($reporteExistente->Nombre_Archivo &&
                        Storage::disk('public')->exists("expedientes/reportes/" . $reporteExistente->Nombre_Archivo)) {
                        Storage::disk('public')->delete("expedientes/reportes/" . $reporteExistente->Nombre_Archivo);
                    }

                    $archivo->storeAs("expedientes/reportes", $nombreArchivo, 'public');
                }

                // Actualizar el registro existente
                $reporteExistente->update([
                    'Periodo_Ini' => $request->fecha_inicio,
                    'Periodo_Fin' => $request->fecha_fin,
                    'Resumen_Actividad' => $request->resumen,
                    'Reporte_Final' => $request->reporte_final ? 1 : 0,
                    'Archivo_Agregado' => $request->hasFile('archivo_pdf') ? 1 : $reporteExistente->Archivo_Agregado,
                    'Calificacion' => null, // reiniciar
                    'Observaciones' => null,
                    'Nombre_Archivo' => $nombreArchivo,
                ]);

                // Actualizar semaforización
                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REPORTE PARCIAL')
                    ->update(['estado' => 'realizado']);

                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REVISIÓN REPORTE PARCIAL')
                    ->update(['estado' => 'proceso']);

                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'CORRECCIÓN REPORTE PARCIAL')
                    ->update(['estado' => 'pendiente']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Reporte {$request->numero_reporte} actualizado (sobrescrito) correctamente",
                    'reporte' => $reporteExistente
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => "Error al actualizar reporte: " . $e->getMessage()], 500);
            }
        }

        // =========================================
        // CASO 2: NO EXISTE → crear nuevo reporte
        // =========================================
        DB::beginTransaction();
        try {
            $nombreArchivo = null;
            $archivoAgregado = 0;

            if ($request->hasFile('archivo_pdf')) {
                $archivo = $request->file('archivo_pdf');
                $timestamp = now()->format('YmdHis');
                $nombreArchivo = "{$claveAlumno}_reporte_{$request->numero_reporte}_{$timestamp}.pdf";

                $archivo->storeAs("expedientes/reportes", $nombreArchivo, 'public');
                $archivoAgregado = 1;
            }

            // Crear nuevo reporte
            $reporte = Reporte::create([
                'Id_Expediente' => $expediente->Id_Expediente,
                'Periodo_Ini' => $request->fecha_inicio,
                'Periodo_Fin' => $request->fecha_fin,
                'Resumen_Actividad' => $request->resumen,
                'Numero_Reporte' => $request->numero_reporte,
                'Reporte_Final' => $request->reporte_final ? 1 : 0,
                'Archivo_Agregado' => $archivoAgregado,
                'Calificacion' => null,
                'Observaciones' => null,
                'Nombre_Archivo' => $nombreArchivo,
            ]);

            // Incrementar contador de reportes
            $expediente->increment('Contador_Reportes');

            // Semaforización
            DB::table('estado_proceso')->updateOrInsert(
                ['clave_alumno' => $claveAlumno, 'etapa' => 'REPORTE PARCIAL'],
                ['estado' => 'realizado']
            );

            DB::table('estado_proceso')->updateOrInsert(
                ['clave_alumno' => $claveAlumno, 'etapa' => 'REVISIÓN REPORTE PARCIAL'],
                ['estado' => 'proceso']
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Reporte {$request->numero_reporte} enviado correctamente",
                'reporte' => $reporte
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($nombreArchivo && Storage::disk('public')->exists("expedientes/reportes/{$nombreArchivo}")) {
                Storage::disk('public')->delete("expedientes/reportes/{$nombreArchivo}");
            }

            return response()->json(['error' => "Error al guardar el reporte: " . $e->getMessage()], 500);
        }
    }

    /**
     * Listar todos los reportes del alumno
     */
    public function index()
    {
        $alumno = session('alumno');
        if (!$alumno) {
            return redirect()->route('alumno.login')->withErrors(['error' => 'Sesión expirada']);
        }

        $claveAlumno = $alumno['cve_uaslp'] ?? null;

        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        if (!$solicitud) {
            return redirect()->route('alumno.inicio')->withErrors(['error' => 'No se encontró solicitud FPP01']);
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (!$expediente) {
            return redirect()->route('alumno.inicio')->withErrors(['error' => 'No se encontró expediente']);
        }

        $reportes = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->orderBy('Numero_Reporte')
            ->get();

        return view('alumno.lista_reportes', compact('reportes', 'alumno'));
    }

    /**
     * Descargar PDF del reporte
     */
    public function descargar($id)
    {
        $reporte = Reporte::findOrFail($id);

        // Validación del alumno
        $alumno = session('alumno');
        if (!$alumno) {
            return redirect()->route('alumno.login')->withErrors(['error' => 'Sesión expirada']);
        }

        $claveAlumno = $alumno['cve_uaslp'] ?? null;
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        if (!$solicitud) {
            abort(403, 'No autorizado');
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (!$expediente || $expediente->Id_Expediente !== $reporte->Id_Expediente) {
            abort(403, 'No autorizado');
        }

        if (!$reporte->Nombre_Archivo || !Storage::disk('public')->exists("expedientes/reportes/{$reporte->Nombre_Archivo}")) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->download("expedientes/reportes/{$reporte->Nombre_Archivo}");
    }

    // ========== MÉTODOS PARA ENCARGADO ==========

    public function reportesAlumno($claveAlumno)
    {
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        if (!$solicitud) {
            return redirect()->route('encargado.consultar_alumno')
                ->withErrors(['error' => 'No se encontró solicitud FPP01 para esta clave']);
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (!$expediente) {
            return redirect()->route('encargado.consultar_alumno')
                ->withErrors(['error' => 'No se encontró expediente para esta clave']);
        }

        $reportes = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->orderBy('Numero_Reporte')
            ->get();

        $alumno = [
            'cve_uaslp' => $claveAlumno,
            'nombre_completo' => $solicitud->alumno->Nombre . ' ' .
                               $solicitud->alumno->ApellidoP_Alumno . ' ' .
                               $solicitud->alumno->ApellidoM_Alumno,
            'carrera' => $solicitud->alumno->Carrera ?? 'N/A',
        ];

        return view('encargado.reportes_alumno', compact('reportes', 'alumno', 'expediente'));
    }

    public function reportesPendientes()
    {
        $reportes = Reporte::with(['expediente.solicitudFPP01.alumno'])
            ->whereNull('Calificacion')
            ->orderBy('Id_Reporte', 'asc')
            ->get()
            ->map(function($reporte) {
                $solicitud = $reporte->expediente->solicitudFPP01;
                $alumno = $solicitud->alumno;

                return [
                    'id_reporte' => $reporte->Id_Reporte,
                    'numero_reporte' => $reporte->Numero_Reporte,
                    'clave_alumno' => $alumno->Clave_Alumno,
                    'nombre_alumno' => $alumno->Nombre . ' ' .
                                      $alumno->ApellidoP_Alumno . ' ' .
                                      $alumno->ApellidoM_Alumno,
                    'carrera' => $alumno->Carrera ?? 'N/A',
                    'periodo_ini' => $reporte->Periodo_Ini,
                    'periodo_fin' => $reporte->Periodo_Fin,
                    'reporte_final' => $reporte->Reporte_Final,
                    'archivo_agregado' => $reporte->Archivo_Agregado,
                    'nombre_archivo' => $reporte->Nombre_Archivo,
                ];
            });

        return view('encargado.reportes_pendientes', compact('reportes'));
    }

    function normalizarEtapa($texto) {
        return trim(preg_replace('/\(\d+\)/', '', $texto));
    }

    public function revisar($id)
    {
        $reporte = Reporte::with(['expediente.solicitudFPP01.alumno'])->findOrFail($id);

        $alumno = $reporte->expediente->solicitudFPP01->alumno;
        $alumnoInfo = [
            'cve_uaslp' => $alumno->Clave_Alumno,
            'nombre_completo' => $alumno->Nombre . ' ' .
                               $alumno->ApellidoP_Alumno . ' ' .
                               $alumno->ApellidoM_Alumno,
            'carrera' => $alumno->Carrera ?? 'N/A',
        ];

        return view('encargado.revisar_reporte', compact('reporte', 'alumnoInfo'));
    }

    /**
     * Aprobar/Calificar un reporte
     */
    public function aprobar(Request $request, $id)
    {
        $request->validate([
            'calificacion' => 'required|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $reporte = Reporte::findOrFail($id);

        // Obtener alumno
        $expediente = $reporte->expediente;
        $solicitud = $expediente->solicitudFPP01;
        $alumno = $solicitud->alumno;
        $claveAlumno = $alumno->Clave_Alumno;

        DB::beginTransaction();

        try {
            // Guardar calificación
            $reporte->update([
                'Calificacion' => $request->calificacion,
                'Observaciones' => $request->observaciones,
            ]);

            if ($request->calificacion < 60) {
                // ❌ REPROBADO
                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REPORTE PARCIAL')
                    ->update(['estado' => 'realizado']);

                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REVISIÓN REPORTE PARCIAL')
                    ->update(['estado' => 'realizado']);

                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'CORRECCIÓN REPORTE PARCIAL')
                    ->update(['estado' => 'proceso']);

            } else if ($request->calificacion >= 60) {
                // Verificar si pasa a reporte final
                $todosAprobados = $this->todosReportesAprobados($expediente);

                if ($todosAprobados) {
                    // Activar REPORTE FINAL
                    DB::table('estado_proceso')->updateOrInsert(
                        ['clave_alumno' => $claveAlumno, 'etapa' => 'REPORTE FINAL'],
                        ['estado' => 'proceso']
                    );

                    DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                        ->where('etapa', 'REPORTE PARCIAL')
                        ->update(['estado' => 'realizado']);

                    DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                        ->where('etapa', 'REVISIÓN REPORTE PARCIAL')
                        ->update(['estado' => 'realizado']);

                    DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                        ->where('etapa', 'CORRECCIÓN REPORTE PARCIAL')
                        ->update(['estado' => 'realizado']);
                } else {
                    // 🟢 APROBADO pero aún no todos los reportes → mantener etapas parciales
                    DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                        ->where('etapa', 'REPORTE PARCIAL')
                        ->update(['estado' => 'proceso']);

                    DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                        ->where('etapa', 'REVISIÓN REPORTE PARCIAL')
                        ->update(['estado' => 'pendiente']);

                    DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                        ->where('etapa', 'CORRECCIÓN REPORTE PARCIAL')
                        ->update(['estado' => 'pendiente']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reporte calificado exitosamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al calificar: ' . $e->getMessage()
            ], 500);
        }
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

    protected function actualizarEstadoReporteFinal($expediente, $claveAlumno)
    {
        // Obtener los reportes permitidos según la carrera
        $carreraAlumno = $expediente->solicitudFPP01->alumno->Carrera ?? null;
        if (!$carreraAlumno) return;

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

                if (strpos($v, ',') !== false) {
                    $parts = array_map('trim', explode(',', $v));
                    return collect($parts);
                }

                if (strpos($v, '-') !== false) {
                    [$a, $b] = array_map('intval', explode('-', $v));
                    return collect(range(min($a, $b), max($a, $b)));
                }

                return collect([$v]);
            })->map(fn($x) => intval($x))
            ->filter(fn($n) => $n > 0)
            ->unique()
            ->sort()
            ->values();

            $allowedReportes = ($numbers->count() === 1 && $numbers[0] > 1)
                ? collect(range(1, $numbers[0]))
                : $numbers;
        }

        // Verificar si todos los reportes permitidos están aprobados
        $reportesAprobados = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->whereIn('Numero_Reporte', $allowedReportes)
            ->where('Calificacion', '>=', 60)
            ->pluck('Numero_Reporte');

        if ($allowedReportes->diff($reportesAprobados)->isEmpty()) {
            // Todos los reportes requeridos aprobados → actualizar estado a REPORTE FINAL
            DB::table('estado_proceso')->updateOrInsert(
                ['clave_alumno' => $claveAlumno, 'etapa' => 'REPORTE FINAL'],
                ['estado' => 'proceso']
            );
        }
    }

    public function calificarFinal(Request $request, $id)
    {
        $request->validate([
            'calificacion' => 'required|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $reporte = Reporte::findOrFail($id);

        if (!$reporte->Reporte_Final) {
            return response()->json(['error' => 'Este no es un reporte final'], 422);
        }

        $expediente = $reporte->expediente;
        $claveAlumno = $expediente->solicitudFPP01->alumno->Clave_Alumno;

        DB::beginTransaction();
        try {
            // Guardar calificación y observaciones
            $reporte->update([
                'Calificacion' => $request->calificacion,
                'Observaciones' => $request->observaciones,
            ]);

            if ($request->calificacion < 60) {
                // ❌ REPROBADO → enviar a corrección
                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REPORTE FINAL')
                    ->update(['estado' => 'realizado']);

                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REVISIÓN REPORTE FINAL')
                    ->update(['estado' => 'realizado']);

                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'CORRECCIÓN REPORTE FINAL')
                    ->update(['estado' => 'proceso']);

                $mensaje = 'Reporte final reprobado y enviado a corrección';
            } else {
                // ✅ APROBADO
                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REPORTE FINAL')
                    ->update(['estado' => 'realizado']);

                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REVISIÓN REPORTE FINAL')
                    ->update(['estado' => 'realizado']);

                DB::table('estado_proceso')->where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'CORRECCIÓN REPORTE FINAL')
                    ->update(['estado' => 'realizado']);

                // Activar etapa de calificación final
                DB::table('estado_proceso')->updateOrInsert(
                    ['clave_alumno' => $claveAlumno, 'etapa' => 'CALIFICACIÓN REPORTE FINAL'],
                    ['estado' => 'proceso']
                );

                $mensaje = 'Reporte final aprobado';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $mensaje
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al calificar reporte final: ' . $e->getMessage()], 500);
        }
    }

    public function descargarEncargado($id)
    {
        $reporte = Reporte::findOrFail($id);

        if (!$reporte->Nombre_Archivo || !Storage::disk('public')->exists("expedientes/reportes/{$reporte->Nombre_Archivo}")) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->download("expedientes/reportes/{$reporte->Nombre_Archivo}");
    }
}
