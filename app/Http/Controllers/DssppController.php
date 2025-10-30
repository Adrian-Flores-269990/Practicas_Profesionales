<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudFPP01;
use App\Models\AutorizacionSolicitud;
use App\Models\EstadoProceso;
use App\Models\CarreraIngenieria;

class DssppController extends Controller
{
    /**
     * 📋 Lista de solicitudes que llegan al Departamento DSSPP.
     */
    public function index()
    {
        // 🔹 Solo solicitudes autorizadas por el encargado (Autorizacion = 1)
        $solicitudes = SolicitudFPP01::with('alumno')
            ->where('Autorizacion', 1)
            ->get();

        // 🔹 Separar según estado interno del DSSPP
        $registros = SolicitudFPP01::with('alumno')
            ->where('Autorizacion', 1)
            ->where('Estado_Departamento', 'aprobado')
            ->get();

        $rechazadas = SolicitudFPP01::with('alumno')
            ->where('Autorizacion', 1)
            ->where('Estado_Departamento', 'rechazado')
            ->get();

        // 🔹 Obtener todas las carreras (para los filtros del buscador)
        $carreras = CarreraIngenieria::orderBy('Descripcion_Capitalizadas')->get();

        // 🔹 Enviar todo a la vista (evita "undefined variable")
        return view('dsspp.solicitudes_alumnos', [
            'solicitudes' => $solicitudes ?? collect(),
            'registros'   => $registros ?? collect(),
            'rechazadas'  => $rechazadas ?? collect(),
            'carreras'    => $carreras ?? collect(),
        ]);
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
     * 🧾 Revisión y autorización de solicitudes por DSSPP.
     */
    public function autorizarSolicitud(Request $request, $id)
    {
        $request->validate([
            'comentario_departamento' => 'nullable|string|max:500',
        ]);

        // 🔹 Capturar decisiones por sección
        $decisiones = [
            'seccion_solicitante' => $request->input('seccion_solicitante'),
            'seccion_empresa'     => $request->input('seccion_empresa'),
            'seccion_proyecto'    => $request->input('seccion_proyecto'),
            'seccion_horario'     => $request->input('seccion_horario'),
            'seccion_creditos'    => $request->input('seccion_creditos'),
        ];

        // ✅ Si todas las secciones son "1", se aprueba
        $autorizado = collect($decisiones)->every(fn($v) => $v === '1') ? 1 : 0;

        // 🔹 Guardar decisión en la tabla de autorizaciones
        AutorizacionSolicitud::updateOrCreate(
            ['Id_Solicitud_FPP01' => $id],
            [
                'Autorizo_Departamento' => $autorizado,
                'Comentario_Departamento' => $request->comentario_departamento,
                'Fecha_Ad' => now(),
            ]
        );

        // 🔹 Actualizar la solicitud principal
        $solicitud = SolicitudFPP01::find($id);
        if ($solicitud) {
            $solicitud->Estado_Departamento = $autorizado ? 'aprobado' : 'rechazado';
            $solicitud->save();

            $claveAlumno = $solicitud->Clave_Alumno;
            $estadoEncargado = $solicitud->Estado_Encargado ?? null;

            // ⚙️ Actualizar los estados del flujo
            if ($autorizado) {
                // Etapa completada
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'realizado']);

                // Si el encargado ya aprobó también → siguiente etapa
                if ($estadoEncargado === 'aprobado') {
                    EstadoProceso::where('clave_alumno', $claveAlumno)
                        ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                        ->update(['estado' => 'proceso']);
                }
            } else {
                // Rechazo → regresa etapas
                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'pendiente']);

                EstadoProceso::where('clave_alumno', $claveAlumno)
                    ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                    ->update(['estado' => 'proceso']);
            }
        }

        return redirect()
            ->route('dsspp.solicitudes')
            ->with('success', 'Revisión del departamento registrada correctamente.');
    }

    /**
     * 👩‍🎓 Consultar datos de alumno por clave.
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
