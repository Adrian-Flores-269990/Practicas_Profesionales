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

class SolicitudController extends Controller
{
    public function store(StoreSolicitudRequest $request)
    {
        try {
            DB::transaction(function() use ($request) {
                // Crear solicitud
                $solicitud = SolicitudFPP01::create([
                    'Fecha_Solicitud' => now(),
                    'Numero_Creditos' => $request->creditos,
                    'Induccion_Placticas' => false,
                    'Clave_Alumno' => auth()->clave_alumno ?? '000000',
                    'Tipo_Seguro' => 'No definido',
                    'NSF' => '',
                    'Situacion_Alunmno_Pasante' => '',
                    'Estadistica_General' => '',
                    'Constancia_Vig_Der' => '',
                    'Carta_Pasante' => '',
                    'Egresado_Sit_Esp' => '',
                    'Archivo_CVD' => '',
                    'Fecha_Inicio' => $request->fecha_inicio,
                    'Fecha_Termino' => $request->fecha_termino,
                    'Clave_Encargado' => '',
                    'Clave_Asesor_Externo' => null,
                    'Datos_Asesor_Externo' => '',
                    'Productos_Servicios_Emp' => '',
                    'Datos_Empresa' => '',
                    'Nombre_Proyecto' => $request->titulo_proyecto,
                    'Actividades' => $request->actividades ?? '',
                    'Horario_Mat_Ves' => $request->turno ?? '',
                    'Horario_Entrada' => '',
                    'Horario_Salida' => '',
                    'Dias_Semana' => '',
                    'Validacion_Creditos' => false,
                    'Apoyo_Economico' => $request->apoyo_economico ?? 0,
                    'Extension_Practicas' => '',
                    'Expedicion_Recibos' => '',
                    'Autorizacion' => false,
                    'Propuso_Empresa' => false,
                    'Evaluacion' => '',
                    'Cancelar' => false,
                ]);

                // Crear empresa (con datos básicos o default)
                $empresa = DependenciaEmpresa::create([
                    'Nombre_Depn_Emp' => $request->nombre_dependencia ?? '',
                    'Calle' => $request->calle ?? '',
                    'Numero' => $request->numero ?? '',
                    'Colonia' => $request->colonia ?? '',
                    'Cp' => $request->cp ?? '',
                    'Estado' => $request->estado ?? '',
                    'Municipio' => $request->municipio ?? '',
                    'RFC_Empresa' => $request->rfc ?? '',
                    'Razon_Social' => $request->razon_social ?? '',
                ]);

                // DependenciaEmpresaSolicitud y sector (igual con defaults)
                $sectorId = null;
                $sectorTipo = $request->sector ?? 'privado';

                if ($sectorTipo == 'privado') {
                    $sector = SectorPrivado::create([
                        'Area_Depto' => $request->area ?? '',
                        'Num_Trabajadores' => $request->num_trabajadores ?? 0,
                        'Actividad_Giro' => $request->actividad_giro ?? '',
                        'Razon_Social' => $request->razon_social ?? '',
                    ]);
                    $sectorId = ['Id_Privado' => $sector->Id_Privado];
                } elseif ($sectorTipo == 'publico') {
                    $sector = SectorPublico::create([
                        'Area_Depto' => $request->area ?? '',
                        'Ambito' => $request->ambito ?? '',
                    ]);
                    $sectorId = ['Id_Publico' => $sector->Id_Publico];
                } elseif ($sectorTipo == 'uaslp') {
                    $sector = SectorUaslp::create([
                        'Area_Depto' => $request->area ?? '',
                        'Tipo_Entidad' => $request->tipo_entidad ?? '',
                        'Id_Entidad_Academica' => $request->entidad_academica ?? null,
                    ]);
                    $sectorId = ['Id_UASLP' => $sector->Id_UASLP];
                }

                DependenciaEmpresaSolicitud::create(array_merge([
                    'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01,
                    'Id_Depend_Emp' => $empresa->Id_Depn_Emp,
                    'Porcentaje' => 100
                ], $sectorId ?? []));
            });

            return redirect()->route('alumno.home')->with('success', 'Solicitud guardada correctamente.');
        } catch (\Exception $e) {
            dd('Error guardando solicitud: '.$e->getMessage());
        }
    }
}
