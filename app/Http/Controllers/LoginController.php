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

        // decodificamos la cadena JSON que viene en "datos"
        $datos = json_decode($datosResponse['datos'], true);

        if (empty($datos) || !is_array($datos)) {
            return back()->withErrors(['cuenta' => 'Error al procesar los datos del alumno']);
        }

        $alumno = $datos[0];

        // Guardar en sesión
        Session::put('alumno', $alumno);

        return redirect()->intended('/alumno/inicio');
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



        // Simulación para pruebas sin API PARA ENCARGADO
        if ($rpe == 123 && $password == 'test') {
            $empleado = [
                'nombre' => 'FROYLAN ELOY HERNANDEZ CASTRO',
                'rpe' => $rpe,
                'rol' => 'ENCARGADO PRACTICAS PROFESIONALES',
                'dependencia' => 'FACULTAD DE INGENIERÍA'            
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('encargado.inicio');
        }


        // Simulación para pruebas sin API PARA ADMINISTRADOR
        if ($rpe == 456 && $password == 'test') {
            $empleado = [
                'nombre' => 'CESAR AUGUSTO PUENTE MONTEJANO',
                'rpe' => $rpe,
                'rol' => 'ADMINISTRADOR',
                'dependencia' => 'FACULTAD DE INGENIERÍA'            
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('administrador.inicio');
        }
        

        // Simulación para pruebas sin API PARA SECRETARIA
        if ($rpe == 789 && $password == 'test') {
            $empleado = [
                'nombre' => 'MOISES ALEJANDRO TORRES TORRES',
                'rpe' => $rpe,
                'rol' => 'SECRETARÍA',
                'dependencia' => 'FACULTAD DE INGENIERÍA'            
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('secretaria.inicio');
        }



        // Simulación para pruebas sin API PARA DSSPP
        if ($rpe == 987 && $password == 'test') {
            $empleado = [
                'nombre' => 'EDGAR IVAN AVALOS TORRES',
                'rpe' => $rpe,
                'rol' => 'DEPARTAMENTO DE SERVICIOS ESCOLARES Y PRÁCTICAS PROFESIONALES',
                'dependencia' => 'FACULTAD DE INGENIERÍA'       
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('dsspp.inicio');
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

        $datos = json_decode($datosResponse['datos'], true);

        if (empty($datos) || !is_array($datos)) {
            return back()->withErrors(['rpe' => 'Error al procesar los datos del empleado']);
        }

        $empleado = $datos[0];
        session(['empleado' => $empleado]);

        return redirect()->route('encargado.inicio');
    }
}
