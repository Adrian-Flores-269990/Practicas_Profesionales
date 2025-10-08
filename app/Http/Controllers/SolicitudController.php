<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolicitudRequest;
use App\Models\SolicitudFPP01;
use App\Models\DependenciaEmpresa;
use App\Models\DependenciaEmpresaSolicitud;
use App\Models\SectorPrivado;
use App\Models\SectorPublico;
use App\Models\SectorUaslp;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    //public function store(StoreSolicitudRequest $request)
    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                // === 1. CREAR SOLICITUD ===
                $solicitud = SolicitudFPP01::create([
                    'Fecha_Solicitud' => now(),
                    'Numero_Creditos' => (int) $request->creditos,
                    'Induccion_Placticas' => 0,
                    'Clave_Alumno' => auth('alumno')->check()
                        ? auth('alumno')->user()->Clave_Alumno
                        : '000000',
                    'Tipo_Seguro' => 0,
                    'NSF' => '',
                    'Situacion_Alunmno_Pasante' => 0,
                    'Estadistica_General' => 0,
                    'Constancia_Vig_Der' => 0,
                    'Carta_Pasante' => 0,
                    'Egresado_Sit_Esp' => 0,
                    'Archivo_CVD' => 0,
                    'Fecha_Inicio' => $request->fecha_inicio,
                    'Fecha_Termino' => $request->fecha_termino,
                    'Clave_Encargado' => 0,
                    'Clave_Asesor_Externo' => null,
                    'Datos_Asesor_Externo' => '',
                    'Productos_Servicios_Emp' => '',
                    'Datos_Empresa' => '',
                    'Nombre_Proyecto' => $request->titulo_proyecto,
                    'Actividades' => $request->actividades ?? '',
                    'Horario_Mat_Ves' => $request->turno ?? '',
                    'Horario_Entrada' => '00:00:00',
                    'Horario_Salida' => '00:00:00',
                    'Dias_Semana' => '',
                    'Validacion_Creditos' => 0,
                    'Apoyo_Economico' => (float) ($request->apoyo_economico ?? 0),
                    'Extension_Practicas' => 0,
                    'Expedicion_Recibos' => 0,
                    'Autorizacion' => 0,
                    'Propuso_Empresa' => 0,
                    'Evaluacion' => 0,
                    'Cancelar' => 0,
                ]);

                // === 2. CREAR EMPRESA ===
                $empresa = DependenciaEmpresa::create([
                    'Nombre_Depn_Emp' => $request->dependencia ?? '',
                    'Calle' => $request->calle ?? '',
                    'Numero' => $request->numero ?? '',
                    'Colonia' => $request->colonia ?? '',
                    'Cp' => (int) ($request->cp ?? 0),
                    'Estado' => $request->estado ?? '',
                    'Municipio' => $request->municipio ?? '',
                    'RFC_Empresa' => $request->rfc ?? '',
                    'Razon_Social' => $request->razon_social ?? '',
                ]);

                // === 3. CREAR SECTOR SEGÚN TIPO ===
                $sectorTipo = $request->sector;
                $sectorId = [];

                if ($sectorTipo === 'privado') {
                    $sector = SectorPrivado::create([
                        'Area_Depto' => $request->area ?? '',
                        'Num_Trabajadores' => (int) ($request->num_trabajadores ?? 0),
                        'Actividad_Giro' => (int) ($request->actividad_giro ?? 0),
                        'Razon_Social' => $request->razon_social ?? '',
                        'Emp_Outsourcing' => 0,
                        'Razon_Social_Outsourcing' => null,
                    ]);
                    $sectorId = ['Id_Privado' => $sector->Id_Privado];
                } elseif ($sectorTipo === 'publico') {
                    $sector = SectorPublico::create([
                        'Area_Depto' => $request->area ?? '',
                        'Ambito' => (int) ($request->ambito ?? 0),
                    ]);
                    $sectorId = ['Id_Publico' => $sector->Id_Publico];
                } elseif ($sectorTipo === 'uaslp') {
                    $sector = SectorUaslp::create([
                        'Area_Depto' => $request->area ?? '',
                        'Tipo_Entidad' => (int) ($request->tipo_entidad ?? 0),
                        'Entidad_Academica' => $request->entidad_academica ?? null,
                    ]);
                    $sectorId = ['Id_UASLP' => $sector->Id_UASLP];
                }

                // === 4. RELACIÓN ENTRE SOLICITUD Y EMPRESA ===
                DependenciaEmpresaSolicitud::create(array_merge([
                    'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01,
                    'Id_Depend_Emp' => $empresa->Id_Depn_Emp,
                    'Porcentaje' => 100.00,
                ], $sectorId));
            });

            return redirect()
            ->route('alumno.home')
            ->with('success', 'Solicitud guardada correctamente.')
            ->with('solicitud_enviada', true);

        } catch (\Exception $e) {
            // Muestra error exacto para debug
            return back()->withErrors(['error' => 'Error guardando solicitud: ' . $e->getMessage()]);
        }
    }
}
