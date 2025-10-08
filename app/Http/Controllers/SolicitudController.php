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
 
                $diasSeleccionados = $request->input('dias_asistencia', []);
                $diasString = implode('', $diasSeleccionados);
                $turno = $request->turno === 'M' ? 'M' : 'V';
 
                // Crear solicitud
                $solicitud = SolicitudFPP01::create([
                    //0 no
                    //1 si
                    'Fecha_Solicitud' => now(), //No lo he checado
                    'Numero_Creditos' => 1, //Nos falta este dato del web service
                    'Induccion_Platicas' => $request->induccionpp === 'si' ? 1 : 0,
                    'Clave_Alumno' => auth()->clave_alumno ?? 1,
                    'Tipo_Seguro' => $request->has('tipo_seguro') ? 1 : 0,
                    'NSF' => $request->nsf,
                    //1 - Alumno
                    //2 - Pasante
                    'Situacion_Alumno_Pasante' => $request->estado === 'alumno' ? 1 : 2,
                    'Estadistica_General' => $request->estadistica_general === 'si' ? 1 : 0,
                    'Constancia_Vig_Der' => $request->constancia_derechos === 'si' ? 1 : 0,
                    'Carta_Pasante' => $request->has('cartapasante') ? 1 : 0,
                    'Egresado_Sit_Esp' => $request->has('egresadosit') ? 1 : 0,
                    'Archivo_CVD' => $request->constancia_derechos === 'si' ? 1 : 0,
                    'Fecha_Inicio' => $request->fecha_inicio, //No lo he checado
                    'Fecha_Termino' => $request->fecha_termino, //No lo he checado
                    'Clave_Encargado' => 1, //Nos falta este dato del web service
                    'Clave_Asesor_Externo' => 1, //Nos falta este dato (No se como manejarlo)
                    'Datos_Asesor_Externo' => 1, //Nos falta este dato (No se como manejarlo)
                    'Productos_Servicios_Emp' => 'No se', //Nos falta este dato (No se como manejarlo)
                    'Datos_Empresa' => 1, //Nos falta este dato (No se como manejarlo)
                    'Nombre_Proyecto' => $request->mombre_proyecto,
                    'Actividades' => $request->actividades,
                    'Horario_Mat_Ves' => $turno,
                    'Horario_Entrada' => $request->horario_entrada,  // HH:MM
                    'Horario_Salida' => $request->horario_salida,   // HH:MM
                    'Dias_Semana' => $diasString,
                    'Validacion_Creditos' => $request->val_creditos === 'si' ? 1 : 0,
                    'Apoyo_Economico' => $request->apoyoeco === 'si' ? 1 : 0,
                    'Extension_Practicas' => $request->extension === 'si' ? 1 : 0,
                    'Expedicion_Recibos' => $request->expe_rec === 'si' ? 1 : 0,
                    'Autorizacion' => false,
                    'Propuso_Empresa' => 0, //Nos falta este dato (No se como manejarlo)
                    'Evaluacion' => 0, //Nos falta este dato (No se como manejarlo)
                    'Cancelar' => 0, //Nos falta este dato (No se como manejarlo)
                ]);

                // Crear empresa (con datos básicos o default)
                $empresa = DependenciaEmpresa::create([
                    'Nombre_Depn_Emp' => $request->dependencia ?? '',
                    'Clasificacion' => 'lol',
                    'Calle' => $request->calle ?? '',
                    'Numero' => $request->numero ?? '',
                    'Colonia' => $request->colonia ?? '',
                    'Cp' => $request->cp ?? 1,
                    'Estado' => $request->estado ?? '',
                    'Municipio' => $request->municipio ?? '',
                    'Telefono' => '333',
                    'Ramo' => 1,
                    'RFC_Empresa' => '',
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

            return redirect()->route('alumno.inicio')->with('success', 'Solicitud guardada correctamente.');
        } catch (\Exception $e) {
            dd('Error guardando solicitud: '.$e->getMessage());
        }
    }
}
