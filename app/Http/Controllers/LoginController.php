<?php

namespace App\Http\Controllers;

use App\Services\UaslpApiService;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Empleado;
use Illuminate\Support\Facades\Schema;

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

    // LOGIN ALUMNO
    public function login(Request $request)
    {
        $request->validate([
            'cuenta' => 'required|string',
            'password' => 'required|string',
        ]);

        $cuenta = $request->input('cuenta');
        $password = $request->input('password');

        // Login contra el web service (tipo 'a' para alumno)
        $loginResponse = $this->uaslpApi->login($cuenta, $password, 'a');
        if (!($loginResponse['correcto'] ?? false)) {
            return back()->withErrors(['cuenta' => 'Credenciales inválidas o servicio no disponible.']);
        }

        // La API de datos de alumno espera la clave numérica sin prefijo 'a' o 'u'
        $claveReal = preg_replace('/^[au]/i', '', $cuenta);
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

        // Encargado
        if ($rpe == 123 && $password == 'test') {
            $empleado = [ '0' => [
                'rpe' => $rpe,
                'nombre' => 'FROYLAN ELOY HERNANDEZ CASTRO',
                'cargo' => 'Catedrático',
                'correo_electronico' => 'cambiar',
                'telefono' => 'cambiar',
                'clave_area' => 'cambiar',
                'area' => 'cambiar',
                'clave_carrera' => 'cambiar',
                'carrera' => 'cambiar',
                ],
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('encargado.inicio');
        }

        // Administrador
        if ($rpe == 456 && $password == 'test') {
            $empleado = [ '0' => [
                    'rpe' => $rpe,
                    'nombre' => 'CESAR AUGUSTO PUENTE MONTEJANO',
                    'cargo' => 'Catedrático',
                    'correo_electronico' => 'cambiar',
                    'telefono' => 'cambiar',
                    'clave_area' => 'cambiar',
                    'area' => 'cambiar',
                    'clave_carrera' => 'cambiar',
                    'carrera' => 'cambiar',
                ],
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('administrador.inicio');
        }

        // Secretaría
        if ($rpe == 789 && $password == 'test') {
            $empleado = [ '0' => [
                'rpe' => $rpe,
                'nombre' => 'MOISES ALEJANDRO TORRES TORRES',
                'cargo' => 'Catedrático',
                'correo_electronico' => 'cambiar',
                'telefono' => 'cambiar',
                'clave_area' => 'cambiar',
                'area' => 'cambiar',
                'clave_carrera' => 'cambiar',
                'carrera' => 'cambiar',
                ],
            ];
            session(['empleado' => $empleado]);
            return redirect()->route('secretaria.inicio');
        }

        // DSSPP
        if ($rpe == 987 && $password == 'test') {
            $empleado = [ '0' => [
                'rpe' => $rpe,
                'nombre' => 'EDGAR IVAN AVALOS TORRES',
                'cargo' => 'Catedrático',
                'correo_electronico' => 'cambiar',
                'telefono' => 'cambiar',
                'clave_area' => 'cambiar',
                'area' => 'cambiar',
                'clave_carrera' => 'cambiar',
                'carrera' => 'cambiar',
                ],
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

    // Regresa el rol del empleado, si no existe lo crea y devuelve null (sin rol)
    public function rolEmpleado($rpeEmpleado, $datosEmpleado)
    {
        $empleadoQuery = Empleado::where('RPE', $rpeEmpleado);

        // Si no existe el empleado lo creamos y regresamos null (sin rol asignado todavía)
        if ($empleadoQuery->count() === 0) {
            $claveArea = null; $area = null; $claveCarrera = null; $carrera = null;
            if (isset($datosEmpleado[0]['carrera']) && $datosEmpleado[0]['carrera'] !== 'NULL') {
                $resultadoClaveArea = []; $resultadoArea = [];
                $resultadoClaveCarrera = []; $resultadoCarrera = [];
                foreach ($datosEmpleado as $item) {
                    $resultadoClaveArea[] = $item['clave_area'] ?? null;
                    $resultadoArea[] = $item['area'] ?? null;
                    $resultadoClaveCarrera[] = $item['clave_carrera'] ?? null;
                    $resultadoCarrera[] = $item['carrera'] ?? null;
                }
                $claveArea = implode(',', array_filter($resultadoClaveArea, fn($v) => $v !== null && $v !== ''));
                $area = implode(',', array_filter($resultadoArea, fn($v) => $v !== null && $v !== ''));
                $claveCarrera = implode(',', array_filter($resultadoClaveCarrera, fn($v) => $v !== null && $v !== ''));
                $carrera = implode(',', array_filter($resultadoCarrera, fn($v) => $v !== null && $v !== ''));
            }

            Empleado::create([
                'Nombre' => $datosEmpleado[0]['nombre'] ?? '',
                'RPE' => $datosEmpleado[0]['rpe'] ?? $rpeEmpleado,
                'Clave_Area' => $claveArea,
                'Area' => $area,
                'Clave_Carrera' => $claveCarrera,
                'Carrera' => $carrera,
                'Cargo' => $datosEmpleado[0]['cargo'] ?? '',
                'Correo' => $datosEmpleado[0]['correo_electronico'] ?? '',
                'Telefono' => $datosEmpleado[0]['telefono'] ?? '',
            ]);
            return null;
        }

        // Si existe exactamente uno, intentar obtener el rol de manera segura
        if ($empleadoQuery->count() === 1) {
            $table = (new Empleado)->getTable();
            // Detectar nombre de la columna de rol (Id_Rol o Rol) para evitar error SQL
            $rolColumn = null;
            if (Schema::hasColumn($table, 'Id_Rol')) {
                $rolColumn = 'Id_Rol';
            } elseif (Schema::hasColumn($table, 'Rol')) {
                $rolColumn = 'Rol';
            }
            // Si no existe ninguna columna de rol, regresamos null (flujo cae al default)
            return $rolColumn ? $empleadoQuery->value($rolColumn) : null;
        }

        // Más de uno (inconsistencia) → devolver null para no romper flujo
        return null;
    }
}