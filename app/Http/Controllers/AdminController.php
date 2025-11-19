<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Rol;
use App\Models\Alumno;
use App\Models\CarreraIngenieria;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     *  Mostrar lista de empleados con su rol
     */
    public function index()
    {
        $empleados = Empleado::with('rol')->get();
        $roles = Rol::all();

               // Agregamos empleados sin rol (para el modal de "Agregar Empleado")
        $empleadosSinRol = Empleado::whereNull('Id_Rol')
            ->orWhere('Id_Rol', 0)
            ->get();

        return view('administrador.empleados', compact('empleados', 'roles', 'empleadosSinRol'));
    }

    /**
     *  Actualizar el rol de un empleado (desde la tabla o modal de edición)
     */
    public function actualizarRol(Request $request, $id)
    {
        $request->validate([
            'Id_Rol' => 'required|exists:roles,id'
        ]);

        $empleado = Empleado::findOrFail($id);
        $empleado->Id_Rol = $request->Id_Rol;
        $empleado->save();

        return redirect()->route('administrador.empleados')
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     *  Mostrar formulario para crear nuevo empleado
     */
    public function create()
    {
        $roles = Rol::all();
        return view('administrador.crear_empleado', compact('roles'));
    }

    /*
     *  Guardar un nuevo empleado en la base de datos
     */
    public function store(Request $request)
    {
         $request->validate([
            'Nombre' => 'required|string|max:255',
            'RPE' => 'required|string|max:100',
            'Correo' => 'nullable|email',
            'Id_Rol' => 'nullable|exists:roles,id',
        ]);

        Empleado::create([
            'RPE' => $request->RPE,
            'Nombre' => $request->Nombre,
            'Clave_Area' => $request->Clave_Area,
            'Area' => $request->Area,
            'Clave_Carrera' => $request->Clave_Carrera,
            'Carrera' => $request->Carrera,
            'Cargo' => $request->Cargo,
            'Correo' => $request->Correo,
            'Telefono' => $request->Telefono,
            'Id_Rol' => $request->Id_Rol,
        ]);

        return redirect()->route('administrador.empleados')
            ->with('success', 'Empleado agregado correctamente.');
    }

    /**
     *  Mostrar formulario de edición (si usas página aparte)
     */
    public function edit($id)
    {
        $empleado = Empleado::findOrFail($id);
        $roles = Rol::all();
        return view('administrador.editar_empleado', compact('empleado', 'roles'));
    }

    /**
     *  Actualizar datos de un empleado (usado también por el modal)
     */
    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);

        $empleado->update([
            'RPE' => $request->RPE,
            'Nombre' => $request->Nombre,
            'Clave_Area' => $request->Clave_Area,
            'Area' => $request->Area,
            'Clave_Carrera' => $request->Clave_Carrera,
            'Carrera' => $request->Carrera,
            'Cargo' => $request->Cargo,
            'Correo' => $request->Correo,
            'Telefono' => $request->Telefono,
            'Id_Rol' => $request->Id_Rol,
        ]);

        return redirect()->route('administrador.empleados')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     *  Eliminar un empleado
     */
    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->delete();

        return redirect()->route('administrador.empleados')
            ->with('success', 'Empleado eliminado correctamente.');
    }

    /**
     *  Nuevo método: Asignar rol a un empleado existente sin rol
     */
    public function asignarRol(Request $request)
    {
        $request->validate([
            'Id_Encargado' => 'required|exists:encargado,Id_Encargado',
            'Id_Rol' => 'required|exists:roles,id',
        ]);

        $empleado = Empleado::findOrFail($request->Id_Encargado);
        $empleado->Id_Rol = $request->Id_Rol;
        $empleado->save();

        return redirect()->route('administrador.empleados')
            ->with('success', 'Rol asignado correctamente a ' . $empleado->Nombre . '.');
    }

    public function actualizarImagen(Request $request, $nombre)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagen = $request->file('imagen');
        $rutaDestino = public_path('images/' . $nombre);

        // Reemplazar imagen existente
        $imagen->move(public_path('images'), $nombre);

        return back()->with('success', 'Imagen actualizada correctamente.');
    }

    /**
     * Consultar alumno por clave o apellidos
     */
    public function consultarAlumno(Request $request)
    {
        $alumnos = [];

        // Si hay búsqueda
        if ($request->has('busqueda') && !empty($request->busqueda)) {
            $busqueda = trim($request->busqueda);

            // Buscar SOLO alumnos que tienen al menos una solicitud
            $alumnosQuery = Alumno::whereHas('solicitudes') // Solo los que tienen solicitudes
                ->where(function($query) use ($busqueda) {
                    $query->where('Clave_Alumno', 'LIKE', "%{$busqueda}%")
                        ->orWhere('ApellidoP_Alumno', 'LIKE', "%{$busqueda}%")
                        ->orWhere('ApellidoM_Alumno', 'LIKE', "%{$busqueda}%")
                        ->orWhere(DB::raw("CONCAT(ApellidoP_Alumno, ' ', ApellidoM_Alumno)"), 'LIKE', "%{$busqueda}%")
                        ->orWhere(DB::raw("CONCAT(Nombre, ' ', ApellidoP_Alumno, ' ', ApellidoM_Alumno)"), 'LIKE', "%{$busqueda}%");
                })
                ->with('solicitudes') // Cargar sus solicitudes
                ->limit(10)
                ->get();

            // Mapear a formato esperado por la vista
            $alumnos = $alumnosQuery->map(function($alumno) {
                return [
                    'cve_uaslp' => $alumno->Clave_Alumno,
                    'nombres' => $alumno->Nombre,
                    'paterno' => $alumno->ApellidoP_Alumno,
                    'materno' => $alumno->ApellidoM_Alumno,
                    'carrera' => $alumno->Carrera,
                    'semestre' => $alumno->Semestre,
                    'creditos' => $alumno->Creditos,
                    'correo' => $alumno->CorreoElectronico,
                ];
            })->toArray();
        }

        return view('administrador.consultar_alumno', compact('alumnos'));
    }

    public function filtrarAlumnosPorEstado(Request $request)
    {
        $tipo = $request->tipo; // 'proceso' o 'terminado'

        // Alumnos que tienen etapas registradas
        $alumnos = Alumno::whereHas('estados') // solo los que tienen etapas
            ->where(function($query) use ($tipo) {
                if ($tipo === 'terminado') {
                    // Deben tener la constancia final NO en pendiente
                    $query->whereHas('estados', function ($q) {
                        $q->where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
                        ->where('estado', '!=', 'pendiente');
                    });
                }
                if ($tipo === 'proceso') {
                    // Tienen etapas, pero NO tienen constancia final completada
                    $query->whereDoesntHave('estados', function ($q) {
                        $q->where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
                        ->where('estado', '!=', 'pendiente');
                    });
                }
            })
            ->with('estados')
            ->get()
            ->map(function($alumno) {
                return [
                    'cve_uaslp' => $alumno->Clave_Alumno,
                    'nombres' => $alumno->Nombre,
                    'paterno' => $alumno->ApellidoP_Alumno,
                    'materno' => $alumno->ApellidoM_Alumno,
                    'carrera' => $alumno->Carrera,
                    'semestre' => $alumno->Semestre,
                    'creditos' => $alumno->Creditos,
                    'correo' => $alumno->CorreoElectronico,
                ];
            });

        return view('administrador.consultar_alumno', [
            'alumnos' => $alumnos,
            'filtro' => $tipo
        ]);
    }

    public function indexEncargados()
    {
        $encargados = Empleado::where('Id_Rol', 2)->get();

        foreach ($encargados as $e) {

            // Convertir "ING, ING2" → ['ING', 'ING2']
            $carreras = $e->Carrera ? array_map('trim', explode(',', $e->Carrera)) : [];

            $e->listaCarreras = $carreras;

            // ---- CONTADORES ----

            // Solicitudes pendientes
            $e->pendientes_solicitudes = DB::table('solicitud_fpp01 as s')
                ->join('alumno as a', 's.Clave_Alumno', '=', 'a.Clave_Alumno')
                ->whereIn('a.Carrera', $carreras)
                ->where('s.Autorizacion', '1')
                ->count();

            // Reportes pendientes (sin estado, contamos todos o filtras por calificación null)
            $e->pendientes_reportes = DB::table('reporte as r')
                ->join('expediente as ex', 'r.Id_Expediente', '=', 'ex.Id_Expediente')
                ->join('solicitud_fpp01 as s', 'ex.Id_Solicitud_FPP01', '=', 's.Id_Solicitud_FPP01')
                ->join('alumno as a', 's.Clave_Alumno', '=', 'a.Clave_Alumno')
                ->whereIn('a.Carrera', $carreras)
                ->whereNull('r.Calificacion') // ⬅️ si eso define "pendiente"
                ->count();

            // Evaluaciones pendientes
            $e->pendientes_evaluaciones = DB::table('evaluacion as ev')
                ->join('solicitud_fpp01 as s', 'ev.Id_Solicitud_FPP01', '=', 's.Id_Solicitud_FPP01')
                ->join('alumno as a', 's.Clave_Alumno', '=', 'a.Clave_Alumno')
                ->whereIn('a.Carrera', $carreras)
                ->where('ev.Estado', '1')
                ->count();
        }

        $carreras = CarreraIngenieria::all();

        return view('administrador.encargados', compact('encargados', 'carreras'));
    }

    public function updateCarreras(Request $request, $id)
    {
        // Obtener el empleado
        $encargado = Empleado::findOrFail($id);

        // Convertir el array en string separado por comas
        $carrerasSeleccionadas = implode(",", $request->carreras);

        // Guardarlo
        $encargado->Carrera = $carrerasSeleccionadas;
        $encargado->save();

        return redirect()->route('administrador.encargados')
            ->with('success', 'Carreras actualizadas correctamente.');
    }

    public function indexEmpresas()
    {
        // Cargar todas las relaciones necesarias
        $empresas = \App\Models\DependenciaMercadoSolicitud::with([
            'dependenciaEmpresa',
            'sectorPrivado',
            'sectorPublico',
            'sectorUaslp'
        ])->get();

        return view('administrador.empresas', compact('empresas'));
    }

    public function constancias(Request $request)
    {
        $query = Alumno::whereHas('estados', function ($q) {
            $q->where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
            ->where('estado', '!=', 'pendiente');
        });

        if ($request->busqueda) {
            $query->where(function ($q) use ($request) {
                $q->where('Clave_Alumno', 'like', "%{$request->busqueda}%")
                ->orWhere('Nombre', 'like', "%{$request->busqueda}%")
                ->orWhere('ApellidoP_Alumno', 'like', "%{$request->busqueda}%")
                ->orWhere('ApellidoM_Alumno', 'like', "%{$request->busqueda}%");
            });
        }

        if ($request->fecha_inicio) {
            $query->whereHas('estados', fn($q) =>
                $q->whereDate('updated_at', '>=', $request->fecha_inicio)
            );
        }

        if ($request->fecha_termino) {
            $query->whereHas('estados', fn($q) =>
                $q->whereDate('updated_at', '<=', $request->fecha_termino)
            );
        }

        $alumnos = $query->with('estados')->get();

        return view('administrador.constancias', compact('alumnos'));
    }
}