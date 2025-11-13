<?php

namespace App\Http\Controllers;

use App\Services\UaslpApiService;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Empleado;

class LoginController extends Controller
{
    protected $uaslpApi;

    public function __construct(UaslpApiService $uaslpApi)
    {
        $this->uaslpApi = $uaslpApi;
    }

    // Arriba de la clase o como método privado
    private function datosArray($response) {
        $datos = $response['datos'] ?? null;
        if (is_null($datos)) return null;
        if (is_string($datos)) {
            $datos = json_decode($datos, true);
        }
        return $datos;
    }

    // Regresa el rol del empleado, en caso de no encontrarse el empleado en la base de datos
    // se agrega sin rol
    public function rolEmpleado($rpeEmpleado, $datosEmpleado)
    {
        $empleado = Empleado::where('RPE', $rpeEmpleado);

        if (!$empleado->count() > 0) {
            // Inicializa variables vacías para evitar el "Undefined variable"
            $claveArea = null;
            $area = null;
            $claveCarrera = null;
            $carrera = null;

            if ($datosEmpleado['0']['carrera'] !== "NULL") {
                foreach ($datosEmpleado as $item) {
                    $resultadoClaveArea[] = $item['clave_area'];
                    $resultadoArea[] = $item['area'];
                    $resultadoClaveCarrera[] = $item['clave_carrera'];
                    $resultadoCarrera[] = $item['carrera'];
                }
                $claveArea = implode(',', $resultadoClaveArea);
                $area = implode(',', $resultadoArea);
                $claveCarrera = implode(',', $resultadoClaveCarrera);
                $carrera = implode(',', $resultadoCarrera);
            }

            Empleado::create([
                'Nombre' => $datosEmpleado['0']['nombre'],
                'RPE' => $datosEmpleado['0']['rpe'],
                'Clave_Area' => $claveArea,
                'Area' => $area,
                'Clave_Carrera' => $claveCarrera,
                'Carrera' => $carrera,
                'Cargo' => $datosEmpleado['0']['cargo'],
                'Correo' => $datosEmpleado['0']['correo_electronico'],
                'Telefono' => $datosEmpleado['0']['telefono'],
            ]);

            return null;
        } else {
            if ($empleado->count() == 1) {
                $rol = Empleado::where('RPE', $rpeEmpleado)->value('Id_Rol');
                return $rol;
            }
        }
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

        // No funcionó el web service
        if (!($loginResponse['correcto'] ?? false)) {
            return back()->withErrors(['cuenta' => 'Fallo en la conexión']);
        }

        /*
        // Verifica contraseña
        $loginDatos = $this->datosArray($loginResponse) ?? [];
        $conexion = $loginDatos[0]['conexion'];
        if ($conexion === false) {
            return back()->withErrors(['cuenta' => 'Datos incorrectos']);
        }*/

        $tipoChar = strtolower($userRaw[0]);
        $claveReal = preg_replace('/^[au]/i', '', $userRaw);

        $datosResponse = $this->uaslpApi->obtenerDatosAlumno($claveReal, 'alumno');

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

        /*
        |--------------------------------------------------------------------------
        | SIMULACIONES LOCALES (SIN API)
        |--------------------------------------------------------------------------
        | Estos accesos sirven para pruebas locales sin depender del web service.
        | Puedes entrar con los siguientes usuarios:
        |   - 123 / test  → Encargado
        |   - 456 / test  → Administrador
        |   - 789 / test  → Secretaría
        |   - 987 / test  → DSSPP
        */
        if ($rpe == 123 && $password == 'test') {
            $empleado = [
                'nombre' => 'FROYLAN ELOY HERNANDEZ CASTRO',
                'rpe' => $rpe,
                'rol' => 'ENCARGADO DE PRÁCTICAS PROFESIONALES',
                'dependencia' => 'FACULTAD DE INGENIERÍA',
                'Id_Rol' => 2, // Encargado
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('encargado.inicio');
        }

        if ($rpe == 456 && $password == 'test') {
            $empleado = [
                'nombre' => 'CESAR AUGUSTO PUENTE MONTEJANO',
                'rpe' => $rpe,
                'rol' => 'ADMINISTRADOR',
                'dependencia' => 'FACULTAD DE INGENIERÍA',
                'Id_Rol' => 1, // Administrador
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('administrador.inicio');
        }

        if ($rpe == 789 && $password == 'test') {
            $empleado = [
                'nombre' => 'MOISES ALEJANDRO TORRES TORRES',
                'rpe' => $rpe,
                'rol' => 'SECRETARÍA',
                'dependencia' => 'FACULTAD DE INGENIERÍA',
                'Id_Rol' => 7, // Secretaría
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('secretaria.inicio');
        }

        if ($rpe == 987 && $password == 'test') {
            $empleado = [
                'nombre' => 'EDGAR IVAN AVALOS TORRES',
                'rpe' => $rpe,
                'rol' => 'DSSPP',
                'dependencia' => 'FACULTAD DE INGENIERÍA',
                'Id_Rol' => 3, // DSSPP
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('dsspp.inicio');
        }

        /*
        |--------------------------------------------------------------------------
        | LOGIN REAL (WEB SERVICE)
        |--------------------------------------------------------------------------
        */
        $loginResponse = $this->uaslpApi->login($rpe, $password, 'u');

        // Si el web service falla
        if (!($loginResponse['correcto'] ?? false)) {
            return back()->withErrors(['cuenta' => 'Fallo en la conexión con el servidor.']);
        }

        $datosResponse = $this->uaslpApi->obtenerDatosEmpleado($rpe, 'empleado');

        if (empty($datosResponse['correcto'])) {
            return back()->withErrors(['rpe' => 'No se pudieron obtener los datos del empleado']);
        }

        $datos = $this->datosArray($datosResponse);
        if (empty($datos) || !is_array($datos)) {
            return back()->withErrors(['rpe' => 'Error al procesar los datos del empleado']);
        }

        $rol = $this->rolEmpleado($rpe, $datos);
        session(['empleado' => $datos]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIONES SEGÚN ROL REAL
        |--------------------------------------------------------------------------
        */
        switch ($rol) {
            case 1:
                return redirect()->route('administrador.inicio'); // Administrador
            case 2:
                return redirect()->route('encargado.inicio');     // Encargado
            case 3:
                return redirect()->route('dsspp.inicio');         // DSSPP
            case 4:
            case null:
                return redirect()->route('encargado.inicio');     // Observador o sin rol
            case 7:
                return redirect()->route('secretaria.inicio');    // Secretaría
            default:
                return redirect()->route('encargado.inicio');     // Por defecto
        }
    }

}