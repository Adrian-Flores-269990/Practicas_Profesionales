<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;

class AlumnoController extends Controller
{
    public function create()
    {
        return view('alumno.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Clave_Alumno' => 'required|integer|unique:alumno,Clave_Alumno',
            'Nombre' => 'required|string|max:100',
            'CorreoElectronico' => 'nullable|email|max:150'
        ]);

        Alumno::create($validated + [
            'ApellidoP_Alumno' => $request->ApellidoP_Alumno,
            'ApellidoM_Alumno' => $request->ApellidoM_Alumno,
            'Semestre' => $request->Semestre,
            'Carrera' => $request->Carrera,
            'TelefonoCelular' => $request->TelefonoCelular,
            'Clave_Materia' => $request->Clave_Materia,
            'Clave_Carrera' => $request->Clave_Carrera,
            'Clave_Area' => $request->Clave_Area,
        ]);

        return back()->with('success', 'Alumno insertado correctamente en la base de datos.');
    }
}
