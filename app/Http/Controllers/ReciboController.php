<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReciboController extends Controller
{
    public function descargar(Request $request)
{
    $request->validate([
        'nombre' => 'required|string',
        'carrera' => 'required|string',
        'clave' => 'required|string',
        'fecha' => 'required|date',
        'periodo' => 'required|string',
        'cantidad' => 'required|numeric',
        'empresa' => 'required|string',
        'autoriza' => 'required|string',
        'telefono_empresa' => 'required|string',
        'cargo' => 'required|string',
        'telefono_alumno' => 'required|string',
        'fecha_entrega' => 'required|date',
        'seguro' => 'required|string',
    ]);

    $data = $request->all();

    $pdf = Pdf::loadView('pdf.recibo', compact('data'));

    return $pdf->download('recibo-ayuda-economica.pdf');
}
}
