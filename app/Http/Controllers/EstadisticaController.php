<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DependenciaEmpresa;
use App\Models\Version;
use App\Models\Evaluacion;
use App\Models\Respuesta;

class EstadisticaController extends Controller
{
    public function index()
    {
        // Obtener todas las empresas (suponiendo que tu modelo Empresa existe)
        $empresas = DependenciaEmpresa::orderBy('Nombre_Depn_Emp')->get();

        // Obtener todas las versiones disponibles
        $versiones = Version::orderBy('Num_Version')->get();

        return view('administrador.estadisticas-empresas', compact('empresas', 'versiones'));
    }

    public function getDatos(Request $request)
    {
        $empresaId = $request->empresa_id;
        $versionId = $request->version_id;

        // Buscar la versión
        $version = Version::find($versionId);
        $versionNombre = $version ? $version->Num_Version : null;

        // Obtener evaluaciones de la empresa y versión
        $evaluaciones = Evaluacion::where('Id_Depn_Emp', $empresaId)
                        ->when($version, function($q) use ($version) {
                            return $q->where('Tipo_Evaluacion', $version->Id_Version);
                        })
                        ->with('respuestas')
                        ->get();

        if ($evaluaciones->isEmpty()) {
            // No hay evaluaciones -> versión 0 y respuestas en 0
            return response()->json([
                'version' => '0',
                'respuestas' => array_fill(0, 13, 0),
                'mensaje' => 'Aún no hay evaluaciones para esta empresa'
            ]);
        }

        $respuestas = [];
        for ($i = 1; $i <= 13; $i++) {
            $total = $evaluaciones->sum(function($ev) use ($i) {
                $resp = $ev->respuestas->where('Id_Pregunta', $i)->first();
                return $resp ? $resp->Valor : 0;
            });
            $respuestas[] = $total / max(1, $evaluaciones->count());
        }

        return response()->json([
            'version' => $versionNombre,
            'respuestas' => $respuestas,
            'mensaje' => ''
        ]);
    }
}
