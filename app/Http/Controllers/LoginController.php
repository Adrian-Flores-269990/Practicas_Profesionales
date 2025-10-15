<?php

namespace App\Http\Controllers;

use App\Services\UaslpApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Alumno;

class LoginController extends Controller
{
    protected $uaslpApi;

    public function __construct(UaslpApiService $uaslpApi)
    {
        $this->uaslpApi = $uaslpApi;
    }

    // arriba de la clase o como método privado
    private function datosArray($response) {
        $datos = $response['datos'] ?? null;
        if (is_null($datos)) return null;
        if (is_string($datos)) {
            $datos = json_decode($datos, true);
        }
        return $datos;
    }

    // --- LOGIN ALUMNO ---
    public function login(Request $request)
    {
        $request->validate([
            'cuenta' => 'required|string',
            'password' => 'required|string',
        ]);

        $clave = $request->input('cuenta');
        $password = $request->input('password');

        $loginResponse = $this->uaslpApi->login($clave, $password, 'a');

        if (!($loginResponse['correcto'] ?? false)) {
            return back()->withErrors(['cuenta' => 'Credenciales incorrectas']);
        }

        $loginDatos = $this->datosArray($loginResponse) ?? [];
        $userRaw = $loginDatos[0]['user'] ?? null;
        if (!$userRaw) {
            return back()->withErrors(['cuenta' => 'Respuesta de login inválida']);
        }

        $tipoChar = strtolower($userRaw[0]);
        $claveReal = preg_replace('/^[au]/i', '', $userRaw);

        $datosResponse = $this->uaslpApi->obtenerDatos($claveReal, 'alumno');

        if (!($datosResponse['correcto'] ?? false)) {
            return back()->withErrors(['cuenta' => 'No se pudieron obtener los datos del alumno']);
        }

        $datos = $this->datosArray($datosResponse);
        if (empty($datos) || !is_array($datos)) {
            return back()->withErrors(['cuenta' => 'Error al procesar los datos del alumno']);
        }

        $alumno = $datos[0];

        // Insertar o actualizar al alumno en la base local
        Alumno::updateOrCreate(
            ['Clave_Alumno' => $alumno['cve_uaslp']],
            [
                'Nombre' => $alumno['nombres'] ?? '',
                'ApellidoP_Alumno' => $alumno['paterno'] ?? '',
                'ApellidoM_Alumno' => $alumno['materno'] ?? '',
                'Semestre' => $alumno['semestre'] ?? '',
                'Carrera' => $alumno['carrera'] ?? '',
                'TelefonoCelular' => $alumno['telefono_celular'] ?? '',
                'CorreoElectronico' => $alumno['correo_electronico'] ?? '',
                'Clave_Carrera' => $alumno['clave_carrera'] ?? null,
                'Clave_Area' => $alumno['clave_area'] ?? null,
                'Clave_Materia' => null,
            ]
        );

        session(['alumno' => $alumno]);
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
