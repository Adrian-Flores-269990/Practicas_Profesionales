<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SolicitudFPP01;
use App\Models\SolicitudFPP02;
use App\Models\AutorizacionSolicitud;
use App\Models\EstadoProceso;
use App\Models\CarreraIngenieria;
use App\Models\Expediente;

class DssppController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudFPP01::with(['alumno', 'autorizaciones'])
            ->where(function ($q) {
                $q->whereDoesntHave('autorizaciones')
                  ->orWhereHas('autorizaciones', function ($q2) {
                      $q2->whereNull('Autorizo_Empleado');
                  });
            })
            ->get();

        $carreras = CarreraIngenieria::orderBy('Descripcion_Capitalizadas')->get();

        return view('dsspp.solicitudes_alumnos', compact('solicitudes', 'carreras'));
    }

    public function verSolicitud($id)
    {
        $solicitud = SolicitudFPP01::with(['alumno', 'autorizaciones'])->findOrFail($id);
        return view('dsspp.revisar_solicitud', compact('solicitud'));
    }

    public function autorizarSolicitud(Request $request, $id)
    {
        $request->validate([
            'accion' => 'required|string|in:aceptar,rechazar',
            'comentario' => 'nullable|string|max:1000',
            'Fecha_Asignacion' => 'required|date',
        ]);

        $solicitud = SolicitudFPP01::with('autorizaciones')->findOrFail($id);
        $nuevoValor = $request->accion === 'aceptar' ? 1 : 0;

        DB::transaction(function () use ($solicitud, $nuevoValor, $request) {
            // Actualiza o crea el registro de autorización
            AutorizacionSolicitud::updateOrCreate(
                ['Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01],
                [
                    'Autorizo_Empleado' => $nuevoValor,
                    'Comentario_Encargado' => $request->input('comentario'),
                    'Fecha_As' => now(),
                ]
            );

            // Cambia estado general de la solicitud
            if ($nuevoValor === 0) {
                $solicitud->Autorizacion = 0;
                $solicitud->Estado_Departamento = 'rechazado';
            } else {
                $solicitud->Estado_Departamento = 'aprobado';
            }

            $solicitud->save();

            
            // Crear o actualizar la solicitud FPP02 con la fecha de asignación
            $registro = SolicitudFPP02::create(
                [
                'Fecha_Asignacion' => $request->Fecha_Asignacion,
                ]
            );
            $idFPP02 = $registro->Id_Solicitud_FPP02;

            $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();
            $expediente->update([
                    'Id_Solicitud_FPP02' => $idFPP02,
                ]);

            // Actualiza los estados del alumno (semaforización)
            $this->actualizarEstadoAlumnoDSSPP(
                $solicitud->Clave_Alumno,
                $solicitud->Estado_Departamento,
                $solicitud->Estado_Encargado
            );
        });

        // Registrar en bitácora según acción
        if ($request->accion === 'aceptar') {
            $this->logBitacora("Aprobación DSSPP a solicitud");
        } else {
            $this->logBitacora("Rechazo DSSPP a solicitud");
        }


        return redirect()
            ->route('dsspp.solicitudes')
            ->with('success', 'Acción realizada correctamente.');
    }

    private function actualizarEstadoAlumnoDSSPP($claveAlumno, $estadoDepto, $estadoEncargado)
    {
        // REGISTRO
        EstadoProceso::updateOrCreate(
            ['clave_alumno' => $claveAlumno, 'etapa' => 'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES'],
            ['estado' => ($estadoDepto === 'rechazado') ? 'proceso' : 'realizado']
        );

        // DSSPP
        EstadoProceso::updateOrCreate(
            ['clave_alumno' => $claveAlumno, 'etapa' => 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)'],
            ['estado' => match (true) {
                $estadoEncargado === 'rechazado' => 'pendiente',
                $estadoDepto === 'aprobado' && $estadoEncargado !== 'rechazado' => 'realizado',
                $estadoDepto === 'rechazado' => 'pendiente',
                default => 'proceso',
            }]
        );

        // ENCARGADO
        EstadoProceso::updateOrCreate(
            ['clave_alumno' => $claveAlumno, 'etapa' => 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)'],
            ['estado' => match ($estadoEncargado) {
                'aprobado' => 'realizado',
                'rechazado' => 'pendiente',
                default => ($estadoDepto === 'aprobado' ? 'proceso' : 'pendiente'),
            }]
        );

        // SIGUIENTE ETAPA
        EstadoProceso::updateOrCreate(
            ['clave_alumno' => $claveAlumno, 'etapa' => 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES'],
            ['estado' => ($estadoEncargado === 'aprobado' && $estadoDepto === 'aprobado') ? 'proceso' : 'pendiente']
        );
    }
}
