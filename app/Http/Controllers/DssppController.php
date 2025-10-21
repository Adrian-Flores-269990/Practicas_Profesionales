<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudFPP01;
use App\Models\EstadoProceso;

class DssppController extends Controller
{
    /**
     * 📋 Lista de solicitudes que llegan al Departamento DSSPP.
     */
    public function index()
    {
        $solicitudes = SolicitudFPP01::with('alumno')->get();
        return view('dsspp.solicitudes_alumnos', compact('solicitudes'));
    }

    /**
     * 🔍 Muestra los datos completos de una solicitud específica.
     */
    public function verSolicitud($id)
    {
        $solicitud = SolicitudFPP01::findOrFail($id);
        return view('dsspp.revisar_solicitud', compact('solicitud'));
    }

    /**
     * ✅ Autorizar o rechazar solicitud por parte del DSSPP.
     */
    public function autorizarSolicitud(Request $request, $id)
    {
        $request->validate([
            'comentario_departamento' => 'nullable|string|max:500',
        ]);

        // ✅ Decisiones por sección (botones Aceptar/Rechazar)
        $decisiones = [
            'seccion_solicitante' => $request->input('seccion_solicitante'),
            'seccion_empresa'     => $request->input('seccion_empresa'),
            'seccion_proyecto'    => $request->input('seccion_proyecto'),
            'seccion_horario'     => $request->input('seccion_horario'),
            'seccion_creditos'    => $request->input('seccion_creditos'),
        ];

        // Si todas las secciones están aceptadas (1) => aprobado
        $autorizado = collect($decisiones)->every(fn($v) => $v === '1') ? 1 : 0;

        // 🔹 Actualizar la solicitud
        $solicitud = SolicitudFPP01::findOrFail($id);
        $solicitud->Estado_Departamento = $autorizado ? 'aprobado' : 'rechazado';
        //$solicitud->Comentario_Departamento = $request->comentario_departamento ?? null;
        $solicitud->save();

        $claveAlumno = $solicitud->Clave_Alumno;
        $estadoEncargado = $solicitud->Estado_Encargado ?? null;

        // ⚙️ Si cualquiera (DSSPP o Encargado) rechaza, se reinicia el flujo
        if ($solicitud->Estado_Departamento === 'rechazado' || $estadoEncargado === 'rechazado') {
            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);

            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);

            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                ->update(['estado' => 'proceso']);
        }
        // ✅ Si ambos aprobaron, se avanza al siguiente estado
        elseif ($solicitud->Estado_Departamento === 'aprobado' && $estadoEncargado === 'aprobado') {
            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                ->update(['estado' => 'realizado']);
        }
        // 🟢 Si DSSPP aprueba pero el encargado aún no revisa, solo marca su parte como "realizado"
        else {
            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'realizado']);
        }

        return redirect()
            ->route('dsspp.solicitudes')
            ->with('success', 'Revisión del departamento registrada correctamente.');
    }

    /**
     * 👩‍🎓 Buscar alumno por clave.
     */
    public function consultarAlumno(Request $request)
    {
        $clave = $request->input('claveAlumno');

        $alumno = \App\Models\Alumno::where('Clave_Alumno', $clave)->first();

        if (!$alumno) {
            return back()->with('error', 'No se encontró ningún alumno con esa clave.');
        }

        return view('dsspp.resultado_alumno', compact('alumno'));
    }
}
