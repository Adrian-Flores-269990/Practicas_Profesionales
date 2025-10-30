<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Rol;

class AdminController extends Controller
{
    /**
     *  Mostrar lista de empleados con su rol
     */
    public function index()
    {
        $empleados = Empleado::with('rol')->get();
        $roles = Rol::all();

        return view('administrador.empleados', compact('empleados', 'roles'));
    }

    /**
     *  Actualizar el rol de un empleado (desde la tabla)
     */
    public function actualizarRol(Request $request, $id)
    {
        $request->validate([
            'Id_Rol' => 'required|exists:roles,id'
        ]);

        $empleado = Empleado::findOrFail($id);
        $empleado->Id_Rol = $request->Id_Rol;
        $empleado->save();

        return redirect()->route('administrador.empleados')->with('success', 'Rol actualizado correctamente.');
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
        // dd($request->all()); // ya comprobamos que llegan los datos

        try {
            Empleado::create([
                'RPE' => $request->RPE,
                'Nombre' => $request->Nombre,
                'Area' => $request->Area,
                'Carrera' => $request->Carrera,
                'Cargo' => $request->Cargo,
                'Correo' => $request->Correo,
                'Telefono' => $request->Telefono,
                'Id_Rol' => $request->Id_Rol,
            ]);

            return redirect()->route('administrador.empleados')->with('success', 'Empleado agregado correctamente.');
        } catch (\Exception $e) {
            dd($e->getMessage()); //  esto mostrará el error exacto de MySQL
        }
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
            'Area' => $request->Area,
            'Carrera' => $request->Carrera,
            'Cargo' => $request->Cargo,
            'Correo' => $request->Correo,
            'Telefono' => $request->Telefono,
            'Id_Rol' => $request->Id_Rol,
        ]);

        return redirect()->route('administrador.empleados')->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     *  Eliminar un empleado
     */
    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->delete();

        return redirect()->route('administrador.empleados')->with('success', 'Empleado eliminado correctamente.');
    }
}
