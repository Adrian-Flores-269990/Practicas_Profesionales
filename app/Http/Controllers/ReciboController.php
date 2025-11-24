<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudFPP01;
use App\Models\Expediente;
use App\Models\SolicitudPago;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReciboController extends Controller
{
    /**
     * Descargar el PDF y registrar la solicitud de pago.
     */
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

        // Generar PDF
        $pdf = Pdf::loadView('pdf.recibo', compact('data'));

        // Resolver Id_Expediente del alumno en sesión (o por clave del form como respaldo)
        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? $request->input('clave');
        $idExpediente = null;

        try {
            $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                ->latest('Id_Solicitud_FPP01')
                ->first();
            if ($solicitud) {
                $exp = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();
                if ($exp) {
                    // Asumimos PK Id_Expediente
                    $idExpediente = $exp->Id_Expediente ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::error('No se pudo resolver Id_Expediente para solicitud de pago: ' . $e->getMessage());
        }

        // Parsear periodo: "DD/MM/AA a DD/MM/AA"
        $periodo = $request->input('periodo');
        $inicio = null; $termino = null;
        if ($periodo) {
            if (preg_match('/^\s*(\d{2}\/\d{2}\/\d{2,4})\s*a\s*(\d{2}\/\d{2}\/\d{2,4})\s*$/', $periodo, $m)) {
                $inicio = $this->parseFechaFlexible($m[1]);
                $termino = $this->parseFechaFlexible($m[2]);
            }
        }

        // Insertar fila en solicitud_pago
        try {
            SolicitudPago::create([
                'Id_Expediente' => $idExpediente,
                'Fecha_Solicitud' => $request->input('fecha'),
                'Fecha_Inicio_Pago' => $inicio,
                'Fecha_Termino_Pago' => $termino,
                'Salario' => $request->input('cantidad'),
                'Nombre_Persona_Autoriza' => $request->input('autoriza'),
                'Cargo_Persona_Autoriza' => $request->input('cargo'),
                'Fecha_Entrega' => $request->input('fecha_entrega'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error creando registro en solicitud_pago: ' . $e->getMessage(), [
                'clave' => $claveAlumno,
                'expediente' => $idExpediente,
            ]);
        }

        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'];
        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $claveAlumno,
                'etapa' => 'SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA'
            ],
            ['estado' => 'realizado']
        );
        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $claveAlumno,
                'etapa' => 'RECIBO DE PAGO'
            ],
            ['estado' => 'proceso']
        );

        // Descargar PDF
        return $pdf->download('recibo-ayuda-economica.pdf');
    }

    /**
     * Convierte una fecha en formato DD/MM/AA(AA) a Y-m-d
     */
    private function parseFechaFlexible(?string $fecha): ?string
    {
        if (!$fecha) return null;
        // Normalizar separadores
        $fecha = trim($fecha);
        // Usar Carbon para flexibilidad
        try {
            // Detectar año corto
            [$d,$m,$y] = explode('/', $fecha);
            if (strlen($y) === 2) {
                // Asumir 20xx para años cortos
                $y = '20' . $y;
            }
            $carbon = Carbon::createFromFormat('d/m/Y', "$d/$m/$y");
            return $carbon->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('No se pudo parsear fecha de periodo', ['valor' => $fecha]);
            return null;
        }
    }

    /**
     * Vista de Recibo de Pago: valida flag de desglose y muestra botón de descarga si existe solicitud_pago.
     */
    public function vistaReciboPago()
    {
        $alumno = session('alumno');
        $clave = $alumno['cve_uaslp'] ?? null;
        if (!$clave) return redirect()->route('alumno.inicio');

        $solicitud = SolicitudFPP01::where('Clave_Alumno', $clave)->latest('Id_Solicitud_FPP01')->first();
        /*
        ***************************** CHECAR ***********************************
        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();
        if (is_null($expediente['Solicitud_de_Recibo']) {
            return redirect()->route('desglosePercepciones.mostrar', [
                'claveAlumno' => $clave,
                'tipo' => 'Carta_Desglose_Percepciones'
            ])->with('error', 'Debes subir la Carta de Desglose de Percepciones primero.');
        }*/

        $idExpediente = $solicitud ? Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->value('Id_Expediente') : null;
        $ultimoPago = null;
        if ($idExpediente) {
            $ultimoPago = SolicitudPago::where('Id_Expediente', $idExpediente)
                ->orderByDesc('Id_Solicitud_Pago')
                ->first();
        }

        return view('alumno.expediente.reciboPago', compact('ultimoPago'));
    }

    /**
     * Genera el PDF del recibo de pago a partir de la última fila en solicitud_pago.
     */
    public function descargarReciboPago()
    {
        $alumno = session('alumno');
        $clave = $alumno['cve_uaslp'] ?? null;
        if (!$clave) return redirect()->route('alumno.inicio');

        $solicitud = SolicitudFPP01::where('Clave_Alumno', $clave)->latest('Id_Solicitud_FPP01')->first();
        if (!$solicitud) return back()->withErrors(['No se encontró la solicitud del alumno.']);

        $idExpediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->value('Id_Expediente');
        if (!$idExpediente) return back()->withErrors(['No se encontró el expediente del alumno.']);

        $pago = SolicitudPago::where('Id_Expediente', $idExpediente)
            ->orderByDesc('Id_Solicitud_Pago')
            ->first();
        if (!$pago) return back()->withErrors(['No hay solicitudes de pago registradas.']);

        $data = [
            'folio' => $pago->Id_Solicitud_Pago,
            'fecha_solicitud' => $pago->Fecha_Solicitud,
            'fecha_inicio' => $pago->Fecha_Inicio_Pago,
            'fecha_termino' => $pago->Fecha_Termino_Pago,
            'salario' => $pago->Salario,
            'autoriza' => $pago->Nombre_Persona_Autoriza,
            'cargo_autoriza' => $pago->Cargo_Persona_Autoriza,
            'fecha_entrega' => $pago->Fecha_Entrega,
        ];

        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'];
        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $claveAlumno,
                'etapa' => 'RECIBO DE PAGO'
            ],
            ['estado' => 'realizado']
        );
        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $claveAlumno,
                'etapa' => 'REPORTE PARCIAL NO. X'
            ],
            ['estado' => 'proceso']
        );

        $pdf = Pdf::loadView('pdf.recibo_pago', compact('data'));
        return $pdf->download('recibo-pago.pdf');

    }
}