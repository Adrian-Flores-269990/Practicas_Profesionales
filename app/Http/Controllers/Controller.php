<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Bitacora;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function logBitacora($movimiento)
    {
        // Alumno UASLP (web service)
        $alumno = session('alumno')['cve_uaslp'] ?? null;

        // Encargado / DSSPP (sesión)
        $empleado = session('empleado')['cve_uaslp'] ?? null;

        // Usuario Laravel (si algún admin usa Auth)
        $authUser = Auth::check() ? Auth::user()->id : null;

        // Determinar clave real
        $clave = $alumno ?? $empleado ?? $authUser;

        if (!$clave) return; // si no hay clave, no registrar

        Bitacora::create([
            'Clave_Usuario' => $clave,
            'Fecha'         => now()->toDateString(),
            'Hora'          => now()->toTimeString(),
            'Movimiento'    => $movimiento,
            'IP'            => request()->ip()
        ]);
    }
}
