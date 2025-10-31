<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bitacora;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::query();

        if ($request->id) $query->where('Clave_Usuario', $request->id);
        if ($request->movimiento) $query->where('Movimiento', 'LIKE', '%'.$request->movimiento.'%');
        if ($request->fecha_inicio) $query->whereDate('Fecha', '>=', $request->fecha_inicio);
        if ($request->fecha_final) $query->whereDate('Fecha', '<=', $request->fecha_final);

        $query = Bitacora::with(['alumno', 'empleado']);

        $registros = $query->orderByDesc('Id_Bitacora')->paginate(10);

        return view('administrador.bitacora', compact('registros'));
    }
}
