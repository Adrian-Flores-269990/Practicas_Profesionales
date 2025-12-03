<?php

namespace App\Http\Controllers;

use App\Models\EstadoProceso;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\SolicitudFPP01;
use App\Models\Expediente;

class SecretariaController extends Controller
{
    public function listarPendientes()
    {
        $constancias = EstadoProceso::where('estado_proceso.etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
            ->where('estado_proceso.estado', 'proceso')
            ->join('alumno', 'alumno.Clave_Alumno', '=', 'estado_proceso.clave_alumno')

            // LEFT JOIN para traer la fecha de liberación del alumno
            ->leftJoin('estado_proceso as liberacion', function($join) {
                $join->on('liberacion.clave_alumno', '=', 'estado_proceso.clave_alumno')
                    ->where('liberacion.etapa', 'LIBERACIÓN DEL ALUMNO');
            })

            ->select(
                'alumno.Clave_Alumno as clave',
                DB::raw("CONCAT(alumno.Nombre, ' ', alumno.ApellidoP_Alumno, ' ', alumno.ApellidoM_Alumno) AS nombre"),
                'alumno.Carrera as carrera',
                DB::raw("liberacion.Fecha_Termino as fecha_liberacion")
            )
            ->get();

        return view('secretaria.generar_constancia', compact('constancias'));
    }

    public function consultarConstancias()
    {
        $constancias = EstadoProceso::where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
            ->where('estado', 'realizado')
            ->join('alumno', 'alumno.Clave_Alumno', '=', 'estado_proceso.clave_alumno')
            ->select(
                'alumno.Clave_Alumno as clave',
                DB::raw("CONCAT(alumno.Nombre, ' ', alumno.ApellidoP_Alumno, ' ', alumno.ApellidoM_Alumno) AS nombre"),
                'alumno.Carrera as carrera',
                'estado_proceso.Fecha_Termino as fecha',
                'estado_proceso.estado as estado'     
            )
            ->get();

        return view('secretaria.consultar_constancias', compact('constancias'));
    }

    public function verConstancia($clave)
    {
        // Carpeta donde guardas las constancias
        $rutaCarpeta = storage_path("app/public/expedientes/Constancia/");

        // Buscar cualquier archivo que coincida con la clave
        $archivos = glob($rutaCarpeta . "{$clave}_constancia_*.pdf");

        if (!$archivos || count($archivos) === 0) {
            return back()->with('error', 'No se encontró la constancia generada para este alumno.');
        }

        // Tomar el archivo más reciente
        $archivo = $archivos[count($archivos) - 1];

        return response()->file($archivo);
    }


    public function generarConstancia($claveAlumno)
    {
        // 1. Buscar solicitud FPP01 autorizada
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->where('Autorizacion', 1)
            ->first();

        if (!$solicitud) {
            return response()->json([
                'success' => false,
                'message' => 'El alumno aún no tiene solicitud autorizada.'
            ]);
        }

        // 2. Buscar o crear expediente
        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (!$expediente) {
            $expediente = Expediente::create([
                'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01
            ]);
        }

        // 3. Nombre único del archivo PDF
        $filename = "{$claveAlumno}_constancia_" . time() . ".pdf";

        try {
            // 4. Generar PDF usando el PdfController
            $pdfController = app(PdfController::class);
            $pdfController->generarConstancia($claveAlumno, $expediente, $filename);

            // 5. Generar la URL pública del archivo PDF
            // (Requiere haber ejecutado: php artisan storage:link)
            $url = asset("storage/expedientes/Constancia/{$filename}");

            return response()->json([
                'success' => true,
                'url' => $url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generando constancia: ' . $e->getMessage()
            ]);
        }
    }

}
