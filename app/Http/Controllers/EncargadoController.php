<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudFPP01;
use App\Models\AutorizacionSolicitud;
use App\Models\EstadoProceso;

class EncargadoController extends Controller
{
    public function index() {
        $solicitudes = SolicitudFPP01::all();
        $solicitudes = SolicitudFPP01::with('alumno')->get();
        return view('encargado.solicitudes_alumnos', compact('solicitudes'));
    }

    public function verSolicitud($id) {
        $solicitud = SolicitudFPP01::findOrFail($id);
        return view('encargado.revisar_solicitud', compact('solicitud'));
    }

    public function revisar($id)
    {
        $solicitud = SolicitudFPP01::with([
            'dependenciaMercadoSolicitud.dependenciaEmpresa',
            'dependenciaMercadoSolicitud.sectorPrivado',
            'dependenciaMercadoSolicitud.sectorPublico',
            'dependenciaMercadoSolicitud.sectorUaslp'
        ])->findOrFail($id);

        return view('encargado.revision', compact('solicitud'));
    }

    public function autorizarSolicitud(Request $request, $id)
    {
        $request->validate([
            'comentario_encargado' => 'nullable|string|max:500',
        ]);

        $decisiones = [
            'seccion_solicitante' => $request->input('seccion_solicitante'),
            'seccion_empresa'     => $request->input('seccion_empresa'),
            'seccion_proyecto'    => $request->input('seccion_proyecto'),
            'seccion_horario'     => $request->input('seccion_horario'),
            'seccion_creditos'    => $request->input('seccion_creditos'),
        ];

        // Guardar decisión general
        $autorizado = collect($decisiones)->every(fn($v) => $v === '1') ? 1 : 0;

        // 🔹 Guardar en autorizacion_solicitud
        $autorizacion = AutorizacionSolicitud::updateOrCreate(
            ['Id_Solicitud_FPP01' => $id],
            [
                'Autorizo_Empleado' => $autorizado,
                'Comentario_Encargado' => $request->comentario_encargado,
                'Fecha_As' => now(),
            ]
        );

        // 🔹 Actualizar también la solicitud principal
        $solicitud = SolicitudFPP01::find($id);
        if ($solicitud) {
            $solicitud->Autorizacion = $autorizado;
            $solicitud->Estado_Encargado = $autorizado ? 'aprobado' : 'rechazado'; // ✅ actualiza el nuevo campo
            $solicitud->save();

            $claveAlumno = $solicitud->Clave_Alumno;

            if ($autorizado) {
                // 1️⃣ Marca la etapa del encargado como completada (verde)
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'realizado']);

                // 2️⃣ Verifica si el DSSPP también ya aprobó
                if ($solicitud->Estado_Departamento === 'aprobado') {
                    EstadoProceso::where('clave_alumno', $claveAlumno)
                        ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                        ->update(['estado' => 'proceso']);
                }

            } else {
                // 🔻 Si rechazó, marca su propia etapa en pendiente
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'pendiente']);

                // 🟡 También regresa el registro de solicitud a "proceso" (porque se reinicia)
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                    ->update(['estado' => 'proceso']);
            }
        }

        return redirect()
            ->route('encargado.solicitudes_alumnos')
            ->with('success', 'Revisión guardada correctamente.');
    }
}
