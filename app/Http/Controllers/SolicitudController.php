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
            
            DB::transaction(function() use ($request) {
                // Crear solicitud
                $solicitud = SolicitudFPP01::create([
                    'Fecha_Solicitud' => now(),
                    'Numero_Creditos' => $request->creditos,
                    'Induccion_Platicas' => false,
                    'Clave_Alumno' => auth()->clave_alumno ?? '000000',
                    'Tipo_Seguro' => '1',
                    'NSF' => '1',
                    'Situacion_Alumno_Pasante' => '1',
                    'Estadistica_General' => '1',
                    'Constancia_Vig_Der' => '1',
                    'Carta_Pasante' => '1',
                    'Egresado_Sit_Esp' => '1',
                    'Archivo_CVD' => '1',
                    'Fecha_Inicio' => $request->fecha_inicio,
                    'Fecha_Termino' => $request->fecha_termino,
                    'Clave_Encargado' => '1',
                    'Clave_Asesor_Externo' => '1',
                    'Datos_Asesor_Externo' => '1',
                    'Productos_Servicios_Emp' => '1',
                    'Datos_Empresa' => '1',
                    'Nombre_Proyecto' => $request->titulo_proyecto,
                    'Actividades' => $request->actividades ?? '',
                    'Horario_Mat_Ves' => $request->turno ?? '',
                    'Horario_Entrada' => '',
                    'Horario_Salida' => '',
                    'Dias_Semana' => '1',
                    'Validacion_Creditos' => false,
                    'Apoyo_Economico' => $request->apoyo_economico ?? 0,
                    'Extension_Practicas' => '1',
                    'Expedicion_Recibos' => '1',
                    'Autorizacion' => false,
                    'Propuso_Empresa' => false,
                    'Evaluacion' => '1',
                    'Cancelar' => false,
                ]);

                // Crear empresa (con datos básicos o default)
                $empresa = DependenciaEmpresa::create([
                    'Nombre_Depn_Emp' => $request->nombre_dependencia ?? '',
                    'Clasificacion' => '',
                    'Calle' => $request->calle ?? '',
                    'Numero' => $request->numero ?? '',
                    'Colonia' => $request->colonia ?? '',
                    'Cp' => $request->cp ?? '',
                    'Estado' => $request->estado ?? '',
                    'Municipio' => $request->municipio ?? '',
                    'RFC_Empresa' => $request->rfc ?? '',
                    'Autorizada' => '',
                ]);

                // DependenciaMercadoSolicitud y sector (igual con defaults)
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

                DependenciaMercadoSolicitud::create(array_merge([
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
