<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudFPP01;
use App\Models\AutorizacionSolicitud;
use Carbon\Carbon;

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
            'dependenciaEmpresaSolicitud.dependenciaEmpresa',
            'dependenciaEmpresaSolicitud.sectorPrivado',
            'dependenciaEmpresaSolicitud.sectorPublico',
            'dependenciaEmpresaSolicitud.sectorUaslp'
        ])->findOrFail($id);

        return view('encargado.revision', compact('solicitud'));
    }

    public function autorizarSolicitud(Request $request, $id)
    {
        $request->validate([
            'comentario_encargado' => 'nullable|string|max:500',
        ]);

        // Recolectar decisiones
        $decisiones = [
            $request->seccion_solicitante,
            $request->seccion_empresa,
            $request->seccion_asesor,
            $request->seccion_proyecto,
            $request->seccion_horario,
            $request->seccion_creditos,
        ];

        // Verificar si todas las secciones fueron aceptadas
        $todasAceptadas = !in_array('0', $decisiones) && !in_array('', $decisiones);

        // Guardar en tabla autorizacion_solicitud
        \App\Models\AutorizacionSolicitud::updateOrCreate(
            ['Id_Solicitud_FPP01' => $id],
            [
                'Autorizo_Empleado' => $todasAceptadas ? 1 : 0,
                'Comentario_Encargado' => $request->comentario_encargado,
                'Fecha_As' => now(),
            ]
        );

        // Si todas aceptadas, actualizar campo "Autorizacion" en solicitud
        if ($todasAceptadas) {
            $solicitud = \App\Models\SolicitudFPP01::find($id);
            $solicitud->Autorizacion = 1;
            $solicitud->save();
        }

        // Redirigir con mensaje
        return redirect()
            ->route('encargado.solicitudes_alumnos')
            ->with('success', 'Revisión guardada correctamente.');
    }
}
