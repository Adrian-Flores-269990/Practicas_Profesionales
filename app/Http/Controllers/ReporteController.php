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

        // Obtener el expediente del alumno
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

        // Obtener reportes existentes para saber cuántos ha subido
        $reportesExistentes = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->orderBy('Numero_Reporte')
            ->get();

        return view('alumno.reporte', compact('alumno', 'expediente', 'reportesExistentes'));
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
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:20480', // 20MB máximo
        ]);

        $alumno = session('alumno');
        if (!$alumno) {
            return response()->json(['error' => 'Sesión expirada'], 401);
        }

        $claveAlumno = $alumno['cve_uaslp'] ?? null;

        // Obtener expediente del alumno
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

        // Verificar si ya existe un reporte con ese número para este expediente
        $reporteExistente = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->where('Numero_Reporte', $request->numero_reporte)
            ->first();

        if ($reporteExistente) {
            return response()->json(['error' => 'Ya existe un reporte con ese número'], 422);
        }

        DB::beginTransaction();
        try {
            $nombreArchivo = null;
            $archivoAgregado = 0;

            // Subir archivo PDF si existe
            if ($request->hasFile('archivo_pdf')) {
                $archivo = $request->file('archivo_pdf');
                $numeroReporte = $request->numero_reporte;
                $timestamp = now()->format('YmdHis');
                
                // Nombre del archivo: clave_reporte_N_timestamp.pdf
                $nombreArchivo = "{$claveAlumno}_reporte_{$numeroReporte}_{$timestamp}.pdf";
                
                // Guardar en storage/app/public/expedientes/reportes/
                $path = $archivo->storeAs(
                    "expedientes/reportes",
                    $nombreArchivo,
                    'public'
                );

                $archivoAgregado = 1;
            }

            // Crear el registro del reporte
            $reporte = Reporte::create([
                'Id_Expediente' => $expediente->Id_Expediente,
                'Periodo_Ini' => $request->fecha_inicio,
                'Periodo_Fin' => $request->fecha_fin,
                'Resumen_Actividad' => $request->resumen,
                'Numero_Reporte' => $request->numero_reporte,
                'Reporte_Final' => $request->reporte_final ? 1 : 0,
                'Archivo_Agregado' => $archivoAgregado,
                'Calificacion' => null, // Será asignada por el encargado
                'Observaciones' => null, // Será asignada por el encargado
                'Nombre_Archivo' => $nombreArchivo,
            ]);

            // Actualizar contador de reportes en expediente
            $expediente->increment('Contador_Reportes');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reporte enviado exitosamente',
                'reporte' => $reporte
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Si hubo error y ya se subió el archivo, eliminarlo
            if ($nombreArchivo && Storage::disk('public')->exists("expedientes/reportes/{$nombreArchivo}")) {
                Storage::disk('public')->delete("expedientes/reportes/{$nombreArchivo}");
            }

            return response()->json([
                'error' => 'Error al guardar el reporte: ' . $e->getMessage()
            ], 500);
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

        // Verificar que el alumno de la sesión sea el dueño del reporte
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

    /**
     * Ver los reportes de un alumno específico (para encargado)
     */
    public function reportesAlumno($claveAlumno)
    {
        // Obtener información del alumno
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

        // Obtener todos los reportes del alumno
        $reportes = Reporte::where('Id_Expediente', $expediente->Id_Expediente)
            ->orderBy('Numero_Reporte')
            ->get();

        // Información del alumno desde la solicitud
        $alumno = [
            'cve_uaslp' => $claveAlumno,
            'nombre_completo' => $solicitud->alumno->Nombre . ' ' . 
                               $solicitud->alumno->ApellidoP_Alumno . ' ' . 
                               $solicitud->alumno->ApellidoM_Alumno,
            'carrera' => $solicitud->alumno->Carrera ?? 'N/A',
        ];

        return view('encargado.reportes_alumno', compact('reportes', 'alumno', 'expediente'));
    }

    /**
     * Ver todos los reportes pendientes de revisión (para encargado)
     */
    public function reportesPendientes()
    {
        // Obtener todos los reportes sin calificación (pendientes de revisar)
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

    /**
     * Mostrar el formulario para revisar un reporte específico (para encargado)
     */
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
     * Aprobar/Calificar un reporte (para encargado)
     */
    public function aprobar(Request $request, $id)
    {
        $request->validate([
            'calificacion' => 'required|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $reporte = Reporte::findOrFail($id);

        $reporte->update([
            'Calificacion' => $request->calificacion,
            'Observaciones' => $request->observaciones,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reporte calificado exitosamente',
        ]);
    }

    /**
     * Descargar PDF del reporte (para encargado)
     */
    public function descargarEncargado($id)
    {
        $reporte = Reporte::findOrFail($id);

        if (!$reporte->Nombre_Archivo || !Storage::disk('public')->exists("expedientes/reportes/{$reporte->Nombre_Archivo}")) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->download("expedientes/reportes/{$reporte->Nombre_Archivo}");
    }
}
