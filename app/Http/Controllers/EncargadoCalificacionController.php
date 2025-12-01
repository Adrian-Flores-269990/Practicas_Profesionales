<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudFPP01;
use App\Models\EstadoProceso;
use Illuminate\Support\Facades\DB;

class EncargadoCalificacionController extends Controller
{
    public function index()
    {
        $alumnos = SolicitudFPP01::select(
                'solicitud_fpp01.*',
                'alumno.Nombre as NombreAlumno',
                'alumno.Carrera',
                'estado_proceso.estado'
            )
            ->join('alumno', 'alumno.Clave_Alumno', '=', 'solicitud_fpp01.Clave_Alumno')
            ->join('estado_proceso', function($join) {
                $join->on('estado_proceso.clave_alumno', '=', 'solicitud_fpp01.Clave_Alumno')
                     ->where('estado_proceso.etapa', '=', 'CALIFICACION FINAL');
            })
            ->where('estado_proceso.estado', 'proceso')
            ->get();

        foreach ($alumnos as $alumno) {

            $expediente = DB::table('expediente')
                ->where('Id_Solicitud_FPP01', $alumno->Id_Solicitud_FPP01)
                ->first();

            if ($expediente) {
                $calificaciones = DB::table('reporte')   // ← SINGULAR
                    ->where('Id_Expediente', $expediente->Id_Expediente)
                    ->pluck('Calificacion');

                $alumno->promedio = $calificaciones->avg() ?? 0;
                $alumno->totalReportes = $calificaciones->count();
            }
        }

        return view('encargado.calificacion.lista', compact('alumnos'));
    }

    public function ver($idSolicitud)
    {
        $solicitud = SolicitudFPP01::findOrFail($idSolicitud);

        $expediente = DB::table('expediente')
            ->where('Id_Solicitud_FPP01', $idSolicitud)
            ->first();

        $reportes = DB::table('reporte')  // ← SINGULAR
            ->where('Id_Expediente', $expediente->Id_Expediente)
            ->orderBy('Numero_Reporte')
            ->get();

        $promedio = round($reportes->avg('Calificacion'), 1);

        return view('encargado.calificacion.ver', [
            'idSolicitud' => $solicitud->Id_Solicitud_FPP01,
            'solicitud'   => $solicitud,
            'alumno'      => $solicitud->alumno,
            'expediente'  => $expediente,
            'reportes'    => $reportes,
            'promedio'    => $promedio
        ]);
    }

    public function aprobar($idSolicitud)
    {
        $solicitud = SolicitudFPP01::findOrFail($idSolicitud);
        $claveAlumno = $solicitud->Clave_Alumno;

        EstadoProceso::where('clave_alumno', $claveAlumno)
            ->where('etapa', 'CALIFICACION FINAL')
            ->update(['estado' => 'realizado']);
        
        EstadoProceso::where('clave_alumno', $claveAlumno)
            ->where('etapa', 'EVALUACIÓN DEL ALUMNO')
            ->update(['estado' => 'realizado']);

        EstadoProceso::where('clave_alumno', $claveAlumno)
            ->where('etapa', 'LIBERACIÓN DEL ALUMNO')
            ->update(['estado' => 'proceso']);

        return redirect()
            ->route('encargado.inicio')
            ->with('success', 'Alumno aprobado correctamente.');
    }

    public function rechazar($idSolicitud)
    {
        $solicitud = SolicitudFPP01::findOrFail($idSolicitud);
        $claveAlumno = $solicitud->Clave_Alumno;

        EstadoProceso::where('clave_alumno', $claveAlumno)
            ->where('etapa', 'CALIFICACION FINAL')
            ->update(['estado' => 'pendiente']);

        return redirect()
            ->route('encargado.inicio')
            ->with('success', 'Alumno marcado como rechazado.');
    }
}
