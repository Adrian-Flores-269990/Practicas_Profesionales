<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudFPP01;
use App\Models\Alumno;

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

    public function generarFpp02(Request $request)
    {
        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? 'sinclave';

        // Obtener datos (ajusta según tus modelos)
        $solicitud = \App\Models\SolicitudFPP01::where('Clave_Alumno', $claveAlumno)->latest()->first();
        $empresa = $solicitud ? $solicitud->empresa : null;

        if (!$solicitud) {
            return back()->withErrors(['No se encontró la información de la solicitud.']);
        }

        // Generar PDF desde la vista
        $pdf = Pdf::loadView('pdfs.fpp02', compact('alumno', 'empresa', 'solicitud'));

        $nombreArchivo = 'FPP02_' . $claveAlumno . '_' . time() . '.pdf';

        // Descargar directamente
        return $pdf->download($nombreArchivo);
    }

    public function generarFpp02Ajax(Request $request)
    {
        try {
            $alumno = session('alumno');
            $claveAlumno = $alumno['cve_uaslp'] ?? null;

            if (!$claveAlumno) {
                return response()->json(['error' => 'No se encontró sesión del alumno.'], 400);
            }

            $solicitud = \App\Models\SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                ->where('Estado_Departamento', 'aprobado')
                ->where('Estado_Encargado', 'aprobado')
                ->latest('Id_Solicitud_FPP01')
                ->first();

            if (!$solicitud) {
                return response()->json(['error' => 'No se encontró solicitud aprobada.'], 404);
            }

            $datosAlumno = \App\Models\Alumno::where('cve_uaslp', $claveAlumno)->first();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.fpp02', [
                'solicitud' => $solicitud,
                'alumno' => $datosAlumno,
            ])->setPaper('letter', 'portrait');

            $nombreArchivo = $claveAlumno . '_fpp02_' . time() . '.pdf';
            $ruta = 'expedientes/fpp02/' . $nombreArchivo;

            if (!Storage::disk('public')->exists('expedientes/fpp02')) {
                Storage::disk('public')->makeDirectory('expedientes/fpp02');
            }

            Storage::disk('public')->put($ruta, $pdf->output());

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="Formato_FPP02.pdf"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar FPP02 (AJAX): ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error interno.'], 500);
        }
    }

}
