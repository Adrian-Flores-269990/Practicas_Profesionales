<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EstadoProceso;
use App\Models\SolicitudFPP01;
use App\Models\Expediente;

class PdfController extends Controller
{

    public function mostrarDocumento($claveAlumno, $tipo)
    {
        $pdfPath = null;

        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                    ->where('Autorizacion', 1)
                                    ->first();

        if (! $solicitud) {
            return abort(404, 'Solicitud no autorizada');
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if ($expediente && isset($expediente[$tipo])) {
            $rutaRelativa = "expedientes/{$tipo}/" . $expediente[$tipo];

            if (Storage::disk('public')->exists($rutaRelativa)) {
                $pdfPath = asset('storage/' . $rutaRelativa);
            }
        }

        // Siempre retorna la vista con pdfPath (puede ser null)
        if ($tipo === 'Carta_Aceptacion') {
            return view('alumno.expediente.cartaAceptacion', compact('pdfPath'));
        }
        if ($tipo === 'Carta_Desglose_Percepciones') {
            return view('alumno.expediente.desglosePercepciones', compact('pdfPath'));
        }

        return abort(404, 'Tipo de documento no válido');
    }

    public function eliminarDocumento(Request $request, $claveAlumno, $tipo)
    {
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                    ->where('Autorizacion', 1)
                                    ->first();

        if (! $solicitud) {
            return abort(404, 'Solicitud no autorizada');
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (! $expediente || ! isset($expediente[$tipo])) {
            return redirect()->back()->with('error', 'No se encontró el documento a eliminar.');
        }

        $rutaRelativa = "expedientes/{$tipo}/" . $expediente[$tipo];

        if (Storage::disk('public')->exists($rutaRelativa)) {
            Storage::disk('public')->delete($rutaRelativa);
        }

        // Quitar el nombre en expediente
        $expediente->$tipo = null;
        $expediente->save();

        return redirect()->back()->with('success', 'Documento eliminado correctamente.');
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
        $ruta = 'expedientes/carta_Aceptacion/' . $nombreArchivo;

        // Asegurarse de que el directorio existe en el disco public
        if (!Storage::disk('public')->exists('expedientes/Carta_Aceptacion')) {
            Storage::disk('public')->makeDirectory('expedientes/Carta_Aceptacion');
        }

        $saved = Storage::disk('public')->putFileAs('expedientes/Carta_Aceptacion', $file, $nombreArchivo);

        // Log para depuración
        Log::info('Intento de guardar archivo (public disk)', [
            'nombreArchivo' => $nombreArchivo,
            'ruta' => $ruta,
            'saved' => $saved,
            'full_path' => storage_path('app/public/' . $ruta),
        ]);

        if ($saved) {

            $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                                    ->where('Autorizacion', 1)
                                                    ->first();
            $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud['Id_Solicitud_FPP01'])
                                                    ->first();
            $expediente->update([
                'Carta_Aceptacion' => $nombreArchivo,
            ]);

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
        $ruta = 'expedientes/Carta_Desglose_Percepciones/' . $nombreArchivo;

        // Asegurarse de que el directorio existe en el disco public
        if (!Storage::disk('public')->exists('expedientes/Carta_Desglose_Percepciones')) {
            Storage::disk('public')->makeDirectory('expedientes/Carta_Desglose_Percepciones');
        }

        $saved = Storage::disk('public')->putFileAs('expedientes/Carta_Desglose_Percepciones', $file, $nombreArchivo);

        // Log para depuración
        Log::info('Intento de guardar desglose percepciones (public disk)', [
            'nombreArchivo' => $nombreArchivo,
            'ruta' => $ruta,
            'saved' => $saved,
            'full_path' => storage_path('app/public/' . $ruta),
        ]);

        if ($saved) {

            $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                                    ->where('Autorizacion', 1)
                                                    ->first();
            $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud['Id_Solicitud_FPP01'])
                                                    ->first();
            $expediente->update([
                'Carta_Desglose_Percepciones' => $nombreArchivo,
            ]);

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
    // PDF FPP02
    // -------------------------------------------------------
    public function generarFpp02(Request $request)
    {
        $alumnoSesion = session('alumno');
        $claveAlumno = $alumnoSesion['cve_uaslp'] ?? null;

        if (!$claveAlumno) {
            return back()->withErrors(['No se encontró la sesión del alumno.']);
        }

        // Obtener solicitud
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

        $dependencia = $solicitud->dependenciaMercadoSolicitud;
        $empresa = optional($dependencia)->dependenciaEmpresa;
        $sector = $dependencia?->Id_Privado
            ? $dependencia->sectorPrivado
            : ($dependencia?->Id_Publico
                ? $dependencia->sectorPublico
                : $dependencia?->sectorUaslp);

        // Generar PDF
        $pdf = Pdf::loadView('pdf.fpp02', [
            'alumno' => $solicitud->alumno,
            'solicitud' => $solicitud,
            'empresa' => $empresa,
            'sector' => $sector,
        ])->setPaper('letter', 'portrait');

        $nombreArchivo = 'FPP02_' . $claveAlumno . '_' . time() . '.pdf';
        $rutaCarpeta = 'expedientes/fpp02';
        $rutaCompleta = $rutaCarpeta . '/' . $nombreArchivo;

        if (!Storage::disk('public')->exists($rutaCarpeta)) {
            Storage::disk('public')->makeDirectory($rutaCarpeta);
        }

        Storage::disk('public')->put($rutaCompleta, $pdf->output());

        // ACTUALIZAR SEMÁFORO
        $this->actualizarSemaforo($claveAlumno, 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES');

        // Guardar sesión
        session(['fpp02_impreso_' . $claveAlumno => true]);

        // Descargar PDF
        $pathAbsoluto = storage_path('app/public/' . $rutaCompleta);
        return response()->download($pathAbsoluto, 'Formato_FPP02.pdf');
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
