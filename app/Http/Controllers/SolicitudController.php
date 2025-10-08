<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolicitudRequest;
use App\Models\SolicitudFPP01;
use App\Models\DependenciaEmpresa;
use App\Models\DependenciaMercadoSolicitud;
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
                    'Induccion_Platicas' => 1,
                    'Clave_Alumno' => auth()->clave_alumno ?? '000000',
                    'Tipo_Seguro' => 1,
                    'NSF' => 'nfnf',
                    'Situacion_Alumno_Pasante' => 1,
                    'Estadistica_General' => 1,
                    'Constancia_Vig_Der' => 1,
                    'Carta_Pasante' => 1,
                    'Egresado_Sit_Esp' => 1,
                    'Archivo_CVD' => 1,
                    'Fecha_Inicio' => $request->fecha_inicio,
                    'Fecha_Termino' => $request->fecha_termino,
                    'Clave_Encargado' => 1,
                    'Clave_Asesor_Externo' => 123,
                    'Datos_Asesor_Externo' => 1,
                    'Productos_Servicios_Emp' => 'dsdsd',
                    'Datos_Empresa' => 1,
                    'Nombre_Proyecto' => $request->titulo_proyecto,
                    'Actividades' => $request->actividades ?? '',
                    'Horario_Mat_Ves' => 'M',
                    'Horario_Entrada' => now(),
                    'Horario_Salida' => now(),
                    'Dias_Semana' => '1',
                    'Validacion_Creditos' => 1,
                    'Apoyo_Economico' => $request->apoyo_economico ?? 0,
                    'Extension_Practicas' => 1,
                    'Expedicion_Recibos' => 1,
                    'Autorizacion' => false,
                    'Propuso_Empresa' => 1,
                    'Evaluacion' => 1,
                    'Cancelar' => 1,
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
                    'Telefono' => '333',
                    'Ramo' => 1,
                    'RFC_Empresa' => $request->rfc ?? '',
                    'Autorizada' => 1,
                ]);

                // DependenciaMercadoSolicitud y sector (igual con defaults)
                /*
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
                }*/

                $mercado = DependenciaMercadoSolicitud::create([
                    'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01,
                    'Id_Depend_Emp' => $empresa->Id_Depn_Emp,
                    'Id_Publico' => 1,
                    'Id_Privado' => 1,
                    'Id_UASLP' => 2,
                    'Id_Mercado' => 1,
                    'Porcentaje' => 100
                ]);
                //], $sectorId ?? []));
            });

            return redirect()->route('alumno.home')->with('success', 'Solicitud guardada correctamente.');
        } catch (\Exception $e) {
            dd('Error guardando solicitud: '.$e->getMessage());
        }
    }
}
