<?php

namespace App\Http\Controllers;

use App\Services\UaslpApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    protected $uaslpApi;

    public function __construct(UaslpApiService $uaslpApi)
    {
        $this->uaslpApi = $uaslpApi;
    }

    // --- LOGIN ALUMNO ---
    public function login(Request $request)
    {
        $request->validate([
            'cuenta' => 'required|string',
            'password' => 'required|string',
        ]);

        $tipo = 'a'; // alumnos
        $clave = $request->input('cuenta');
        $password = $request->input('password');

        $loginResponse = $this->uaslpApi->login($clave, $password, $tipo);

        if (!isset($loginResponse['correcto']) || !$loginResponse['correcto']) {
            return back()->withErrors(['cuenta' => 'Credenciales incorrectas']);
        }

        $datosResponse = $this->uaslpApi->obtenerDatos($clave, 'alumno');
        if (!isset($datosResponse['correcto']) || !$datosResponse['correcto']) {
            return back()->withErrors(['cuenta' => 'No se pudieron obtener los datos del alumno']);
        }

        $alumno = $datosResponse['datos'][0];

        // Guardar en sesión
        Session::put('alumno', $alumno);

        return redirect()->intended('/alumno/home');
    }

    // --- LOGIN EMPLEADO ---
    public function loginEmpleado(Request $request)
    {
        $request->validate([
            'rpe' => 'required|numeric',
            'contrasena' => 'required|string',
        ]);

        $rpe = $request->input('rpe');
        $password = $request->input('contrasena');

        // Simulación para pruebas sin API
        if ($rpe == 12345 && $password == 'test') {
            $empleado = [
                'nombre' => 'FROYLAN ELOY HERNANDEZ CASTRO',
                'rpe' => $rpe,
                'puesto' => 'ASESOR ACADÉMICO',
                'dependencia' => 'FACULTAD DE INGENIERÍA',
                'foto' => 'https://estudiantes.ing.uaslp.mx/escolar/segest/segind/images/0342962jpg'
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('encargado.home');
        }

        // Lógica real
        $loginResponse = $this->uaslpApi->login($rpe, $password, 'u');

        if (!isset($loginResponse['correcto']) || !$loginResponse['correcto']) {
            return back()->withErrors(['rpe' => 'Credenciales incorrectas']);
        }

        $datosResponse = $this->uaslpApi->obtenerDatos($rpe, 'empleado');

        if (!isset($datosResponse['correcto']) || !$datosResponse['correcto']) {
            return back()->withErrors(['rpe' => 'No se pudieron obtener los datos del empleado']);
        }

        $empleado = $datosResponse['datos'][0];
        session(['empleado' => $empleado]);

        return redirect()->route('encargado.home');
    }
}
