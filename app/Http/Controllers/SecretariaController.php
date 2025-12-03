<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\SolicitudFPP01;
use App\Models\Expediente;

class SecretariaController extends Controller
{
    public function generarConstancia($claveAlumno)
    {
        $claveAlumno = "CAMBIAR"; // PARA PRUEBAS
        // Buscar solicitud FPP01 autorizada
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->where('Autorizacion', 1)
            ->first();

        if (! $solicitud) {
            return back()->with('error', 'El alumno aún no tiene solicitud autorizada.');
        }

        // Buscar expediente
        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (! $expediente) {
            $expediente = Expediente::create([
                'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01
            ]);
        }

        // GENERAR CONSTANCIA (solo genera, NO CAMBIA ESTADOS)
        try {
            app(PdfController::class)
                ->generarConstancia($claveAlumno, $expediente);

            return redirect()
                ->route('secretaria.generar_constancia')
                ->with('success', 'Constancia generada correctamente.');
        }
        catch (\Exception $e) {
            return back()->with('error', 'Error generando la carta: ' . $e->getMessage());
        }
    }
}