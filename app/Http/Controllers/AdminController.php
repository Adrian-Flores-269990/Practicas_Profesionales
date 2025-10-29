<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Rol;

class AdminController extends Controller
{
    /**
     * 📋 Mostrar lista de empleados con su rol
     */
    public function index()
    {
        $empleados = Empleado::with('rol')->get();
        $roles = Rol::all();

        return view('administrador.empleados', compact('empleados', 'roles'));
    }

    /**
     * 🔄 Actualizar el rol de un empleado
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
}
