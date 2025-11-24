<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModalController extends Controller
{
    public function update(Request $request, $modal)
    {
        // Validar que sea imagen
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB máximo
        ]);

        // Nombre fijo según modal
        $nombreArchivo = match($modal) {
            'diagrama-proceso' => 'diagrama-proceso.png',
            'proceso-practicas' => 'proceso-practicas.png',
            default => 'otros.png',
        };

        $carpeta = 'images';

        // Crear carpeta si no existe
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }

        // Guardar imagen sobrescribiendo la anterior
        $file = $request->file('imagen');
        if ($file->isValid()) {
            Storage::disk('public')->putFileAs($carpeta, $file, $nombreArchivo);
        }

        return back()->with('success', 'Imagen actualizada correctamente.');
    }
}
