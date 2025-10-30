<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudFPP01;
use App\Models\AutorizacionSolicitud;
use App\Models\EstadoProceso;
use App\Models\CarreraIngenieria;

class EncargadoController extends Controller
{
    public function index()
    {
        // Cargar todas las solicitudes con su relación alumno
        $solicitudes = SolicitudFPP01::with('alumno')->get();

        // Separar solicitudes por estado
        $registros = SolicitudFPP01::with('alumno')
            ->where('Estado_Encargado', 'aprobado')
            ->get();

        $rechazadas = SolicitudFPP01::with('alumno')
            ->where('Estado_Encargado', 'rechazado')
            ->get();

        // Obtener todas las carreras
        $carreras = CarreraIngenieria::orderBy('Descripcion_Capitalizadas')->get();

        // Enviar todo a la vista
        return view('encargado.solicitudes_alumnos', [
            'solicitudes' => $solicitudes ?? collect(),
            'registros'   => $registros ?? collect(),
            'rechazadas'  => $rechazadas ?? collect(),
            'carreras'    => $carreras ?? collect(),
        ]);
    }

    public function verSolicitud($id)
    {
        // Esta vista puede ser la que solo muestra detalles simples
        $solicitud = SolicitudFPP01::findOrFail($id);
        return view('encargado.revisar_solicitud', compact('solicitud'));
    }

    public function revisar($id)
    {
        // Se agregan todas las relaciones necesarias para mostrar la sección completa
        $solicitud = SolicitudFPP01::with([
            'alumno',
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

        // Guardar en autorizacion_solicitud
        AutorizacionSolicitud::updateOrCreate(
            ['Id_Solicitud_FPP01' => $id],
            [
                'Autorizo_Empleado' => $autorizado,
                'Comentario_Encargado' => $request->comentario_encargado,
                'Fecha_As' => now(),
            ]
        );

        // Actualizar también la solicitud principal
        $solicitud = SolicitudFPP01::find($id);
        if ($solicitud) {
            $solicitud->Autorizacion = $autorizado;
            $solicitud->Estado_Encargado = $autorizado ? 'aprobado' : 'rechazado';
            $solicitud->save();

            $claveAlumno = $solicitud->Clave_Alumno;

            if ($autorizado) {
                // Etapa completada (verde)
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'realizado']);

                // Si el DSSPP ya aprobó, avanza la siguiente etapa
                if ($solicitud->Estado_Departamento === 'aprobado') {
                    EstadoProceso::where('clave_alumno', $claveAlumno)
                        ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                        ->update(['estado' => 'proceso']);
                }
            } else {
                // Rechazo → regresa a pendiente y proceso
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'pendiente']);

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
