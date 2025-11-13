<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EstadoProceso;

class PdfController extends Controller
{
    public function eliminarDesglosePercepciones(Request $request)
    {
        $archivo = $request->input('archivo');
        if ($archivo && \Illuminate\Support\Facades\Storage::disk('public')->exists($archivo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($archivo);
            return back()->with('success', 'El archivo fue eliminado correctamente.');
        } else {
            return back()->withErrors(['No se encontró el archivo a eliminar.']);
        }
    }

    public function eliminarCartaAceptacion(Request $request)
    {
        $archivo = $request->input('archivo');
        if ($archivo && \Illuminate\Support\Facades\Storage::disk('public')->exists($archivo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($archivo);
            return back()->with('success', 'El archivo fue eliminado correctamente.');
        } else {
            return back()->withErrors(['No se encontró el archivo a eliminar.']);
        }
    }

    public function subirCartaAceptacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

    $file = $request->file('archivo');
    $alumno = session('alumno');
    $claveAlumno = $alumno['cve_uaslp'] ?? 'sinclave';
    $nombreArchivo = $claveAlumno . '_carta_aceptacion_' . time() . '.' . $file->getClientOriginalExtension();
    $ruta = 'expedientes/carta-aceptacion/' . $nombreArchivo;

        // Asegurarse de que el directorio existe en el disco public
        if (!Storage::disk('public')->exists('expedientes/carta-aceptacion')) {
            Storage::disk('public')->makeDirectory('expedientes/carta-aceptacion');
        }

        $saved = Storage::disk('public')->putFileAs('expedientes/carta-aceptacion', $file, $nombreArchivo);

        // Log para depuración
        Log::info('Intento de guardar archivo (public disk)', [
            'nombreArchivo' => $nombreArchivo,
            'ruta' => $ruta,
            'saved' => $saved,
            'full_path' => storage_path('app/public/' . $ruta),
        ]);

        if ($saved) {
            return back()->with('success', 'Archivo subido correctamente: ' . $nombreArchivo);
        } else {
            return back()->withErrors(['No se pudo guardar el archivo.']);
        }
    }

    public function subirDesglosePercepciones(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

    $file = $request->file('archivo');
    $alumno = session('alumno');
    $claveAlumno = $alumno['cve_uaslp'] ?? 'sinclave';
    $nombreArchivo = $claveAlumno . '_desglose_percepciones_' . time() . '.' . $file->getClientOriginalExtension();
    $ruta = 'expedientes/desglose-percepciones/' . $nombreArchivo;

        // Asegurarse de que el directorio existe en el disco public
        if (!Storage::disk('public')->exists('expedientes/desglose-percepciones')) {
            Storage::disk('public')->makeDirectory('expedientes/desglose-percepciones');
        }

        $saved = Storage::disk('public')->putFileAs('expedientes/desglose-percepciones', $file, $nombreArchivo);

        // Log para depuración
        Log::info('Intento de guardar desglose percepciones (public disk)', [
            'nombreArchivo' => $nombreArchivo,
            'ruta' => $ruta,
            'saved' => $saved,
            'full_path' => storage_path('app/public/' . $ruta),
        ]);

        if ($saved) {
            return back()->with('success', 'Archivo subido correctamente: ' . $nombreArchivo);
        } else {
            return back()->withErrors(['No se pudo guardar el archivo.']);
        }
    }

    // Método eliminado. La lógica de guardado de constancia_pdf se moverá a SolicitudController@store

    // -------------------------------------------------------
    // PDF FPP02
    // -------------------------------------------------------

    private function actualizarSemaforo($claveAlumno, $etapaActual)
    {
        try {
            // Actualizar la etapa actual
            $actual = EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $claveAlumno,
                    'etapa' => $etapaActual,
                ],
                [
                    'estado' => 'realizado',
                ]
            );

            // Lista completa de etapas en orden
            $etapas = [
                'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)',
                'CARTA DE PRESENTACIÓN (DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES)',
                'CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)',
                'CARTA DE PRESENTACIÓN (ALUMNO)',
                'CARTA DE ACEPTACIÓN (ALUMNO)',
                'CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)',
                'CARTA DE DESGLOSE DE PERCEPCIONES',
                'SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA',
                'RECIBO DE PAGO',
                'REPORTE PARCIAL NO. X',
                'REVISIÓN REPORTE PARCIAL NO. X',
                'CORRECCIÓN REPORTE PARCIAL NO. X',
                'REPORTE FINAL',
                'REVISIÓN REPORTE FINAL',
                'CORRECCIÓN REPORTE FINAL',
                'CALIFICACIÓN REPORTE FINAL',
                'CARTA DE TÉRMINO',
                'EVALUACIÓN DE LA EMPRESA',
                'CALIFICACIÓN FINAL',
                'EVALUACIÓN DEL ALUMNO',
                'LIBERACIÓN DEL ALUMNO',
                'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES',
            ];

            // Buscar la siguiente etapa
            $indiceActual = array_search($etapaActual, $etapas);
            if ($indiceActual !== false && isset($etapas[$indiceActual + 1])) {
                $siguienteEtapa = $etapas[$indiceActual + 1];

                EstadoProceso::updateOrCreate(
                    [
                        'clave_alumno' => $claveAlumno,
                        'etapa' => $siguienteEtapa,
                    ],
                    [
                        'estado' => 'pendiente',
                    ]
                );

                Log::info("Siguiente etapa marcada como pendiente", [
                    'clave_alumno' => $claveAlumno,
                    'siguiente' => $siguienteEtapa
                ]);
            }

            Log::info("Etapa actual marcada como realizada", [
                'clave_alumno' => $claveAlumno,
                'actual' => $etapaActual
            ]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar semáforo: " . $e->getMessage());
        }
    }


// -------------------------------------------------------
// PDF FPP02 - ACTUALIZADO CON FORMATO OFICIAL
// -------------------------------------------------------
public function generarFpp02(Request $request)
{
    $alumnoSesion = session('alumno');
    $claveAlumno = $alumnoSesion['cve_uaslp'] ?? null;
    
    if (!$claveAlumno) {
        return back()->withErrors(['No se encontró la sesión del alumno.']);
    }

    // Obtener solicitud con todas las relaciones
    $solicitud = \App\Models\SolicitudFPP01::with([
        'alumno',
        'dependenciaMercadoSolicitud.dependenciaEmpresa',
        'dependenciaMercadoSolicitud.sectorPrivado',
        'dependenciaMercadoSolicitud.sectorPublico',
        'dependenciaMercadoSolicitud.sectorUaslp'
    ])
    ->where('Clave_Alumno', $claveAlumno)
    ->latest('Id_Solicitud_FPP01')
    ->first();

    if (!$solicitud) {
        return back()->withErrors(['No se encontró la información de la solicitud.']);
    }

    // Obtener datos de dependencia y sector
    $dependencia = $solicitud->dependenciaMercadoSolicitud;
    $empresa = optional($dependencia)->dependenciaEmpresa;
    $sector = $dependencia?->Id_Privado
        ? $dependencia->sectorPrivado
        : ($dependencia?->Id_Publico
            ? $dependencia->sectorPublico
            : $dependencia?->sectorUaslp);

    // ⭐ OBTENER ASESOR EXTERNO SI EXISTE
    $asesorExterno = null;
    if ($solicitud->Clave_Asesor_Externo) {
        $asesorExterno = \App\Models\AsesorExterno::find($solicitud->Clave_Asesor_Externo);
    }

    // Preparar datos para el PDF con formato oficial
    $alumnoData = $solicitud->alumno;
    
    $data = [
        // Datos del alumno
        'nombreCompleto' => trim(
            ($alumnoData->Nombre ?? '') . ' ' . 
            ($alumnoData->ApellidoP_Alumno ?? '') . ' ' . 
            ($alumnoData->ApellidoM_Alumno ?? '')
        ),
        'claveUnica' => $alumnoData->Clave_Alumno ?? '',
        'carrera' => $alumnoData->Carrera ?? '',
        
        // Datos de la empresa
        'razonSocial' => $empresa->Nombre_Depn_Emp ?? ($sector->Nombre_Depn_Emp ?? ''),
        'direccionEmpresa' => $this->formatearDireccion($empresa, $sector),
        'rfc' => $empresa->RFC_Empresa ?? '',
        'telefono' => $empresa->Telefono ?? ($sector->Telefono ?? ''),
        
        // Datos del proyecto
        'areaDepartamento' => $sector->Area_Depto ?? ($empresa->Area_Depto ?? ''),
        'nombreProyecto' => $solicitud->Nombre_Proyecto ?? '',
        
        // ⭐ DATOS DEL ASESOR EXTERNO (desde la tabla asesor_externo)
        'nombreAsesor' => $asesorExterno 
            ? trim(
                ($asesorExterno->Nombre ?? '') . ' ' . 
                ($asesorExterno->Apellido_Paterno ?? '') . ' ' . 
                ($asesorExterno->Apellido_Materno ?? '')
              )
            : '',
        'cargoAsesor' => $asesorExterno->Puesto ?? '',
        'telefonoAsesor' => $asesorExterno->Telefono ?? '',
        'emailAsesor' => $asesorExterno->Correo ?? '',
        
        // Fechas y horarios
        'fechaInicial' => $solicitud->Fecha_Inicio 
            ? \Carbon\Carbon::parse($solicitud->Fecha_Inicio)->format('d/m/Y') 
            : '',
        'fechaFinal' => $solicitud->Fecha_Fin 
            ? \Carbon\Carbon::parse($solicitud->Fecha_Fin)->format('d/m/Y') 
            : '',
        'horarioEntrada' => $solicitud->Horario_Entrada ?? '',
        'horarioSalida' => $solicitud->Horario_Salida ?? '',
        
        // Fecha de generación
        'fechaGeneracion' => now()->format('d/m/Y'),
        
        // Pasar objetos completos para uso en la vista (por si acaso)
        'alumno' => $alumnoData,
        'solicitud' => $solicitud,
        'empresa' => $empresa,
        'sector' => $sector,
    ];

    // Generar PDF con el formato oficial FPP02
    $pdf = Pdf::loadView('pdf.fpp02', $data)
        ->setPaper('letter', 'portrait');

    $nombreArchivo = 'FPP02_' . $claveAlumno . '_' . time() . '.pdf';
    $rutaCarpeta = 'expedientes/fpp02';
    $rutaCompleta = $rutaCarpeta . '/' . $nombreArchivo;

    // Crear carpeta si no existe
    if (!Storage::disk('public')->exists($rutaCarpeta)) {
        Storage::disk('public')->makeDirectory($rutaCarpeta);
    }

    // Guardar PDF en storage
    Storage::disk('public')->put($rutaCompleta, $pdf->output());

    // ACTUALIZAR SEMÁFORO
    $this->actualizarSemaforo($claveAlumno, 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES');

    // Guardar sesión
    session(['fpp02_impreso_' . $claveAlumno => true]);

    // Descargar PDF (sin ventana negra)
    return $pdf->download('Formato_FPP02.pdf');
}

// Función auxiliar para formatear dirección
private function formatearDireccion($empresa, $sector)
{
    $entidad = $empresa ?? $sector;
    
    if (!$entidad) {
        return '';
    }
    
    $direccion = trim(
        ($entidad->Calle ?? '') . ' ' . 
        ($entidad->Numero ? '#' . $entidad->Numero : '') . ', ' .
        ($entidad->Colonia ?? '') . ', ' .
        ($entidad->Municipio ?? '') . ', ' .
        ($entidad->Estado ?? '') . ', ' .
        ($entidad->Cp ? 'CP ' . $entidad->Cp : '')
    );
    
    // Limpiar comas y espacios extras
    $direccion = preg_replace('/,\s*,/', ',', $direccion);
    $direccion = preg_replace('/,\s*$/', '', $direccion);
    
    return $direccion;
}

// -------------------------------------------------------
// SUBIR FPP02 FIRMADO
// -------------------------------------------------------
public function subirFpp02Firmado(Request $request)
{
    $request->validate([
        'archivo' => 'required|file|mimes:pdf|max:20480',
    ]);

    $alumno = session('alumno');
    $claveAlumno = $alumno['cve_uaslp'] ?? 'sinclave';

    $file = $request->file('archivo');
    $nombreArchivo = $claveAlumno . '_FPP02_firmado_' . time() . '.pdf';
    $ruta = 'expedientes/fpp02-firmados/' . $nombreArchivo;

    if (!Storage::disk('public')->exists('expedientes/fpp02-firmados')) {
        Storage::disk('public')->makeDirectory('expedientes/fpp02-firmados');
    }

    Storage::disk('public')->putFileAs('expedientes/fpp02-firmados', $file, $nombreArchivo);

    // ACTUALIZAR SEMÁFORO
    $this->actualizarSemaforo($claveAlumno, 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)');

    return back()->with('success', 'El archivo firmado fue subido correctamente.');
}
}
