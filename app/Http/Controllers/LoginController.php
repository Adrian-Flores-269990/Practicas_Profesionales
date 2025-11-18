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

    // Regresa el rol del empleado, en caso de no encontrarse el empleado en la base de datos
    // se agrega sin rol
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
            $table = (new Empleado)->getTable(); // 'encargado'
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

        $loginResponse = $this->uaslpApi->login($rpe, $password, 'u');

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

        $datosResponse = $this->uaslpApi->obtenerDatosEmpleado($rpe, 'empleado');

        if (empty($datosResponse['correcto'])) {
            return back()->withErrors(['rpe' => 'No se pudieron obtener los datos del empleado']);
        }

        $datos = $this->datosArray($datosResponse);
        if (empty($datos) || !is_array($datos)) {
            return back()->withErrors(['rpe' => 'Error al procesar los datos del empleado']);
        }

        $rol = $this->rolEmpleado($rpe, $datos);


        // Aquí solo faltaría cambiar los datos de cada ruta, de momento todos entran a encargado

        session(['empleado' => $datos]);

        // Observador
        if($rol == NULL || $rol == 4){
            return redirect()->route('encargado.inicio');
        }

        // Secretaría
        if($rol == 7){
            return redirect()->route('encargado.inicio');
        }

        // DSSPP
        if($rol == 3){
            return redirect()->route('encargado.inicio');
        }

        // Encargado
        if($rol == 2){
            return redirect()->route('encargado.inicio');
        }

        // Administrador
        if($rol == 1){
            return redirect()->route('encargado.inicio');
        }




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
    }
}
