<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudFPP01;
use App\Models\AutorizacionSolicitud;

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

        // Guardar en autorizacion_solicitud
        $autorizacion = AutorizacionSolicitud::updateOrCreate(
            ['Id_Solicitud_FPP01' => $id],
            [
                'Autorizo_Empleado' => collect($decisiones)->every(fn($v) => $v === '1') ? 1 : 0,
                'Comentario_Encargado' => $request->comentario_encargado,
                'Fecha_As' => now(),
            ]
        );

        // Actualizar también el campo en solicitud
        $solicitud = SolicitudFPP01::find($id);
        if ($solicitud) {
            $solicitud->Autorizacion = $autorizacion->Autorizo_Empleado; // 1 o 0 según decisión
            $solicitud->save();
        }

        return redirect()
            ->route('encargado.solicitudes_alumnos')
            ->with('success', 'Revisión guardada correctamente.');
    }
}
