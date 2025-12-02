<?php

namespace App\Http\Controllers;

use App\Models\EstadoProceso;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SecretariaController extends Controller
{
    public function listarPendientes()
    {
        $constancias = EstadoProceso::where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
            ->where('estado', 'proceso')
            ->join('alumno', 'alumno.Clave_Alumno', '=', 'estado_proceso.clave_alumno')
            ->select(
                'alumno.Clave_Alumno as clave',
                DB::raw("CONCAT(alumno.Nombre, ' ', alumno.ApellidoP_Alumno, ' ', alumno.ApellidoM_Alumno) AS nombre"),
                'alumno.Carrera as carrera',
                'estado_proceso.Fecha_Termino as fecha_termino'
            )
            ->get();

        return view('secretaria.generar_constancia', compact('constancias'));
    }

    public function generarConstancia($clave)
    {
        // Actualizar estado final
        EstadoProceso::where('clave_alumno', $clave)
            ->where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
            ->update([
                'estado' => 'realizado',
                'Fecha_Termino' => now()
            ]);

        // Aquí podrías generar el PDF si ya lo tienes
        // PDFController::generarConstancia($clave);

        // Redirigir al inicio de Secretaría
        return redirect()
            ->route('secretaria.inicio')
            ->with('success', 'Constancia generada correctamente.');
    }

    public function consultarConstancias()
    {
        $constancias = EstadoProceso::where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
            ->where('estado', 'realizado')
            ->join('alumno', 'alumno.Clave_Alumno', '=', 'estado_proceso.clave_alumno')
            ->select(
                'alumno.Clave_Alumno as clave',
                DB::raw("CONCAT(alumno.Nombre, ' ', alumno.ApellidoP_Alumno, ' ', alumno.ApellidoM_Alumno) AS nombre"),
                'alumno.Carrera as carrera',
                'estado_proceso.Fecha_Termino as fecha',
                'estado_proceso.estado as estado'     
            )
            ->get();

        return view('secretaria.consultar_constancias', compact('constancias'));
    }

    public function verConstancia($clave)
    {
        $ruta = storage_path("app/public/constancias/{$clave}.pdf");

        if (!file_exists($ruta)) {
            return redirect()->back()->with('error', 'No se encontró la constancia generada.');
        }

        return response()->file($ruta);
    }
}
