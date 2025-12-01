<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pregunta;
use App\Models\Evaluacion;
use App\Models\Respuesta;
use App\Models\SolicitudFPP01;
use Illuminate\Support\Facades\DB;

class EvaluacionController extends Controller
{
    /**
     * Mostrar el formulario de evaluación para el alumno
     */
    public function mostrarEvaluacion()
    {
        $alumno = session('alumno');
        if (!$alumno) {
            return redirect()->route('alumno.login')->withErrors(['error' => 'Sesión expirada']);
        }

        $claveAlumno = $alumno['cve_uaslp'] ?? null;

        // Obtener la solicitud FPP01 del alumno
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        if (!$solicitud) {
            return redirect()->route('alumno.inicio')->withErrors(['error' => 'No se encontró solicitud FPP01']);
        }

        // Verificar si ya existe una evaluación
        $evaluacionExistente = Evaluacion::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)
            ->where('Tipo_Evaluacion', 'Empresa')
            ->first();

        // Obtener las preguntas para evaluación de Alumno
        $preguntas = Pregunta::where('Tipo_Evaluacion', 'Alumno')
            ->orderBy('Id_Pregunta')
            ->get();

        // Obtener información de la empresa desde la solicitud
        $empresa = null;
        $actividadPrincipal = $solicitud->Nombre_Proyecto ?? 'N/A';

        if ($solicitud->dependenciaMercadoSolicitud && $solicitud->dependenciaMercadoSolicitud->dependenciaEmpresa) {
            $empresa = $solicitud->dependenciaMercadoSolicitud->dependenciaEmpresa->Nombre_Depn_Emp;
        }

        return view('alumno.evaluacion', compact('alumno', 'solicitud', 'preguntas', 'empresa', 'actividadPrincipal', 'evaluacionExistente'));
    }

    /**
     * Guardar la evaluación del alumno
     */
    public function guardarEvaluacion(Request $request)
    {
        $alumno = session('alumno');
        if (!$alumno) {
            return response()->json(['error' => 'Sesión expirada'], 401);
        }

        $claveAlumno = $alumno['cve_uaslp'] ?? null;

        // Obtener la solicitud FPP01 del alumno con sus relaciones
        $solicitud = SolicitudFPP01::with('dependenciaMercadoSolicitud')
            ->where('Clave_Alumno', $claveAlumno)
            ->latest('Id_Solicitud_FPP01')
            ->first();

        if (!$solicitud) {
            return response()->json(['error' => 'No se encontró solicitud FPP01'], 404);
        }

        // Obtener empresa
        $idDepnEmp = $solicitud->dependenciaMercadoSolicitud->Id_Depend_Emp ?? null;
        if (!$idDepnEmp) {
            return response()->json(['error' => 'No se encontró la empresa asociada a la solicitud'], 404);
        }

        DB::beginTransaction();
        try {
            // Verificar si ya existe evaluación
            $evaluacion = Evaluacion::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)
                ->where('Tipo_Evaluacion', 'Empresa')
                ->first();

            if ($evaluacion) {
                $evaluacion->update(['Estado' => 1]);
                Respuesta::where('Id_Evaluacion', $evaluacion->Id_Evaluacion)->delete();
            } else {
                $evaluacion = Evaluacion::create([
                    'Tipo_Evaluacion' => 'Empresa',
                    'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01,
                    'Id_Depn_Emp' => $idDepnEmp,
                    'Estado' => 1
                ]);
            }

            // Guardar respuestas
            $respuestas = $request->input('respuestas', []);
            if (empty($respuestas) || !is_array($respuestas)) {
                DB::rollBack();
                return response()->json(['error' => 'No se recibieron respuestas.'], 422);
            }

            foreach ($respuestas as $idPregunta => $respuesta) {
                if (!empty($respuesta)) {
                    Respuesta::create([
                        'Id_Evaluacion' => $evaluacion->Id_Evaluacion,
                        'Id_Pregunta' => $idPregunta,
                        'Respuesta' => $respuesta
                    ]);
                }
            }

            /*
            =======================================================
             SEMAFORIZACIÓN 
            =======================================================
            */

            // 1. EVALUACIÓN DE LA EMPRESA → REALIZADO
            \App\Models\EstadoProceso::actualizarEstado(
                $claveAlumno,
                'EVALUACIÓN DE LA EMPRESA',
                'realizado'
            );

            // 2. CALIFICACIÓN FINAL → PROCESO (la que sigue)
            \App\Models\EstadoProceso::actualizarEstado(
                $claveAlumno,
                'CALIFICACIÓN FINAL',
                'proceso'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('alumno.estado'),
                'message' => 'Evaluación enviada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
