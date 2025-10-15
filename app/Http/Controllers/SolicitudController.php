<?php

namespace App\Http\Controllers;

use App\Models\SolicitudFPP01;
use App\Models\DependenciaEmpresa;
use App\Models\DependenciaMercadoSolicitud;
use App\Models\SectorPrivado;
use App\Models\SectorPublico;
use App\Models\SectorUaslp;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SolicitudController extends Controller
{
    /**
     * Mostrar el formulario de solicitud
     */
    public function create()
    {
        // Obtener datos del alumno desde la sesión
        $alumno = session('alumno');

        // Si no hay alumno en sesión, redirigir al login
        if (!$alumno) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión primero']);
        }

        // Pasar los datos del alumno a la vista
        return view('alumno.expediente.solicitudFPP01', compact('alumno'));
    }

    /**
     * Guardar la solicitud
     */
    public function store(Request $request)
    {
        try {
            DB::transaction(function() use ($request) {

                // Primero obtener el alumno desde sesión o request
                $alumno = session('alumno');
                $claveAlumno = $alumno['cve_uaslp'] ?? $request->input('clave') ?? $request->input('clave_hidden') ?? null;

                if (empty($claveAlumno)) {
                    Log::warning('Intento de guardar solicitud sin Clave_Alumno. Session alumno: ', ['session_alumno' => $alumno]);
                    throw ValidationException::withMessages([
                        'clave_alumno' => 'No se encontró la clave del alumno. Asegúrate de haber iniciado sesión o de que el web service devuelva la información.'
                    ]);
                }

                // Preparar variables de días y turno
                $diasSeleccionados = $request->input('dias_asistencia', []);
                $diasString = implode('', $diasSeleccionados);
                $turno = $request->turno === 'M' ? 'M' : 'V';

                // Crear la solicitud usando los datos del alumno
                $solicitud = SolicitudFPP01::create([
                    'Fecha_Solicitud' => now(),
                    'Numero_Creditos' => $alumno['creditos'] ?? null,
                    'Induccion_Platicas' => $request->induccionpp === 'si' ? 1 : 0,
                    'Clave_Alumno' => $claveAlumno,
                    'Tipo_Seguro' => $request->has('tipo_seguro') ? 1 : 0,
                    'NSF' => $request->nsf,
                    'Situacion_Alumno_Pasante' => $request->estado === 'alumno' ? 1 : 2,
                    'Estadistica_General' => $request->estadistica_general === 'si' ? 1 : 0,
                    'Constancia_Vig_Der' => $request->constancia_derechos === 'si' ? 1 : 0,
                    'Carta_Pasante' => $request->has('cartapasante') ? 1 : 0,
                    'Egresado_Sit_Esp' => $request->has('egresadosit') ? 1 : 0,
                    'Archivo_CVD' => $request->constancia_derechos === 'si' ? 1 : 0,
                    'Fecha_Inicio' => $request->fecha_inicio,
                    'Fecha_Termino' => $request->fecha_termino,
                    'Clave_Encargado' => 1,
                    'Clave_Asesor_Externo' => 1,
                    'Datos_Asesor_Externo' => 1,
                    'Productos_Servicios_Emp' => 'No se',
                    'Datos_Empresa' => 1,
                    'Nombre_Proyecto' => $request->mombre_proyecto,
                    'Actividades' => $request->actividades,
                    'Horario_Mat_Ves' => $turno,
                    'Horario_Entrada' => $request->horario_entrada,
                    'Horario_Salida' => $request->horario_salida,
                    'Dias_Semana' => $diasString,
                    'Validacion_Creditos' => $request->val_creditos === 'si' ? 1 : 0,
                    'Apoyo_Economico' => $request->apoyoeco === 'si' ? 1 : 0,
                    'Extension_Practicas' => $request->extension === 'si' ? 1 : 0,
                    'Expedicion_Recibos' => $request->expe_rec === 'si' ? 1 : 0,
                    'Autorizacion' => null,
                    'Propuso_Empresa' => 0,
                    'Evaluacion' => 0,
                    'Cancelar' => 0,
                ]);

                // Crear sectores y dependencias como ya lo tenías
                $sectorPrivado = 1;
                $sectorPublico = 1;
                $sectorUASLP = 2;

                if ($request->sector === 'privado') {
                    $nombreEmpresa = $request->nombre_empresa_privado;
                    $clasificacion = '1';
                    $rfc = $request->rfc_privado;
                    $ramo = $request->ramo_privado;
                    $calle = $request->calle_empresa_privado;
                    $numero = $request->numero_empresa_privado;
                    $colonia = $request->colonia_empresa_privado;
                    $cp = $request->cp_empresa_privado;
                    $estado = $request->estado_empresa_privado;
                    $municipio = $request->municipio_empresa_privado;
                    $telefono = $request->telefono_empresa_privado;
                    $areaDepto = $request->area_depto_privado;

                    $sector = SectorPrivado::create([
                        'Area_Depto' => $request->area_depto_privado,
                        'Num_Trabajadores' => $request->num_trabajadores,
                        'Actividad_Giro' => $request->actividad_giro,
                        'Razon_Social' => $request->razon_social,
                        'Emp_Outsourcing' => $request->empresa_outsourcing === 'si' ? 1 : 0,
                        'Razon_Social_Outsourcing' => $request->razon_social_outsourcing
                    ]);
                    $sectorPrivado = $sector->Id_Privado;

                } elseif ($request->sector === 'publico') {
                    $nombreEmpresa = $request->nombre_empresa_publico;
                    $clasificacion = '0';
                    $rfc = $request->rfc_publico;
                    $ramo = $request->ramo_publico;
                    $calle = $request->calle_empresa_publico;
                    $numero = $request->numero_empresa_publico;
                    $colonia = $request->colonia_empresa_publico;
                    $cp = $request->cp_empresa_publico;
                    $estado = $request->estado_empresa_publico;
                    $municipio = $request->municipio_empresa_publico;
                    $telefono = $request->telefono_empresa_publico;
                    $areaDepto = $request->area_depto_publico;

                    $sector = SectorPublico::create([
                        'Area_Depto' => $request->area_depto_publico,
                        'Ambito' => $request->ambito
                    ]);
                    $sectorPublico = $sector->Id_Publico;

                } elseif ($request->sector === 'uaslp') {
                    $sector = SectorUaslp::create([
                        'Area_Depto' => $request->area_depto_uaslp,
                        'Tipo_Entidad' => $request->tipo_entidad,
                        'Id_Entidad_Academica' => $request->entidad_academica
                    ]);
                    $sectorUASLP = $sector->Id_UASLP;
                }

                // Crear relación mercado/dependencia
                DependenciaMercadoSolicitud::create([
                    'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01,
                    'Id_Depend_Emp' => isset($empresa) ? $empresa->Id_Depn_Emp : null,
                    'Id_Publico' => $sectorPublico ?? null,
                    'Id_Privado' => $sectorPrivado ?? null,
                    'Id_UASLP' => $sectorUASLP ?? null,
                    'Id_Mercado' => 1,
                    'Porcentaje' => 100
                ]);

                // Crear empresa si aplica
                if ($request->sector === 'privado' || $request->sector === 'publico') {
                    $empresa = DependenciaEmpresa::create([
                        'Nombre_Depn_Emp' => $nombreEmpresa,
                        'Clasificacion' => $clasificacion,
                        'Calle' => $calle,
                        'Numero' => $numero,
                        'Colonia' => $colonia,
                        'Cp' => $cp,
                        'Estado' => $estado,
                        'Municipio' => $municipio,
                        'Telefono' => $telefono,
                        'Ramo' => $ramo,
                        'RFC_Empresa' => $rfc,
                        'Autorizada' => 0
                    ]);
                }
            });

            return redirect()->route('alumno.inicio')->with('success', 'Solicitud guardada correctamente.');

        } catch (\Exception $e) {
            dd('Error guardando solicitud: ' . $e->getMessage());
        }
    }
}
