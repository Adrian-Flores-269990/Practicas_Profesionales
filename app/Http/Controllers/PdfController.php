<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{
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
}
