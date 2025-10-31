<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SolicitudFPP01;
use App\Models\AutorizacionSolicitud;
use App\Models\EstadoProceso;
use App\Models\CarreraIngenieria;

class EncargadoController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudFPP01::with(['alumno', 'autorizaciones'])
            ->whereHas('autorizaciones', function ($q) {
                $q->whereNotNull('Autorizo_Empleado');
            })
            ->get();

        $carreras = CarreraIngenieria::orderBy('Descripcion_Capitalizadas')->get();

        return view('encargado.solicitudes_alumnos', compact('solicitudes', 'carreras'));
    }

    public function verSolicitud($id)
    {
        $solicitud = SolicitudFPP01::findOrFail($id);
        return view('encargado.revisar_solicitud', compact('solicitud'));
    }

    public function revisar($id)
    {
        $solicitud = SolicitudFPP01::with([
            'alumno',
            'dependenciaMercadoSolicitud.dependenciaEmpresa',
            'dependenciaMercadoSolicitud.sectorPrivado',
            'dependenciaMercadoSolicitud.sectorPublico',
            'dependenciaMercadoSolicitud.sectorUaslp',
            'autorizaciones' // traer la fila de autorizacion_solicitud
        ])->findOrFail($id);

        return view('encargado.revision', compact('solicitud'));
    }

    public function autorizarSolicitud(Request $request, $id)
    {
        $request->validate([
            'seccion_solicitante' => 'nullable|string',
            'seccion_empresa'     => 'nullable|string',
            'seccion_proyecto'    => 'nullable|string',
            'seccion_horario'     => 'nullable|string',
            'seccion_creditos'    => 'nullable|string',
            'comentario_encargado'=> 'nullable|string|max:1000',
        ]);

        $solicitud = SolicitudFPP01::with('autorizaciones')->findOrFail($id);

        // Recolectar decisiones de secciones
        $decisiones = [
            $request->input('seccion_solicitante'),
            $request->input('seccion_empresa'),
            $request->input('seccion_proyecto'),
            $request->input('seccion_horario'),
            $request->input('seccion_creditos'),
        ];

        // Normalizar: '' -> null para facilitar comprobaciones
        $decisionesNorm = array_map(function ($v) {
            if ($v === '') return null;
            return $v;
        }, $decisiones);

        // Determinar decisión del encargado
        if (in_array('0', $decisionesNorm, true)) {
            $encValor = 0; // rechazado
        } else {
            // si todas las secciones están decididas y todas son '1' => aprobado por encargado
            $allDecididas = collect($decisionesNorm)->every(fn($v) => $v !== null);
            $allOnes = $allDecididas && collect($decisionesNorm)->every(fn($v) => $v === '1');
            if ($allOnes) {
                $encValor = 2; // encargado aprobó (valor 2 en autorizacion_solicitud)
            } elseif ($allDecididas && collect($decisionesNorm)->every(fn($v) => $v === '1' || $v === null)) {
                // fallback (no debería entrar): si no hay ceros pero hay null -> pendiente
                $encValor = 1;
            } else {
                // alguna sección sin decidir -> pendiente para el encargado (1)
                $encValor = 1;
            }
        }

        DB::transaction(function () use ($solicitud, $encValor, $request) {
            // Actualizar o crear la fila en autorizacion_solicitud
            $autorizacion = AutorizacionSolicitud::updateOrCreate(
                ['Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01],
                [
                    // Guardamos la decisión del encargado (0,1,2)
                    'Autorizo_Empleado' => $encValor,
                    'Comentario_Encargado' => $request->input('comentario_encargado'),
                    'Fecha_As' => now(),
                    // si tienes columna para registrar id del empleado:
                    // 'Id_Empleado' => auth()->id() ?? null,
                ]
            );

            // Si el encargado rechazó -> la solicitud principal se marca como rechazada (Autorizacion = 0)
            if ($encValor === 0) {
                $solicitud->Autorizacion = 0; // campo de solicitud_fpp01 solo admite 0 o 1
                $solicitud->Estado_Encargado = 'rechazado';
                $solicitud->save();

                // actualizar EstadoProceso en consecuencia
                EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                    ->whereIn('etapa', [
                        'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                        'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES'
                    ])->update(['estado' => 'pendiente']);
            }

            // Si encargado aprobó (valor 2) -> marcamos la solicitud principal como Aprobada (1)
            if ($encValor === 2) {
                $solicitud->Autorizacion = 1; // aprobado final
                $solicitud->Estado_Encargado = 'aprobado';
                $solicitud->save();

                // actualizar EstadoProceso: encargardo realizado y activar siguiente etapa si corresponde
                EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'realizado']);
            }

            // Si pendiente (1) -> no cambiamos solicitud_fpp01.Autorizacion (queda como estaba)
            if ($encValor === 1) {
                // marcar etapa como en proceso/pendiente según tu lógica
                EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                    ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                    ->update(['estado' => 'pendiente']);
            }
        });

        if ($encValor === 0) {
            // Encargado rechazó
            $solicitud->Autorizacion = 0;
            $solicitud->Estado_Encargado = 'rechazado';
            $solicitud->save();

            // Rechazo: regresa flujo
            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);

            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);

            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES')
                ->update(['estado' => 'pendiente']);
        }
        elseif ($encValor === 2) {
            // Encargado aprobó
            $solicitud->Autorizacion = 1;
            $solicitud->Estado_Encargado = 'aprobado';
            $solicitud->save();

            // Marca esta etapa como realizada
            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'realizado']);

            // Activa siguiente etapa (Registro de Autorización de Prácticas)
            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                ->update(['estado' => 'proceso']);
        }
        else {
            // Pendiente
            EstadoProceso::where('clave_alumno', $solicitud->Clave_Alumno)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);
        }

        return redirect()
            ->route('encargado.solicitudes_alumnos')
            ->with('success', 'Revisión guardada correctamente.');
    }
}
