<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EvaluacionController extends Controller
{
    public function index()
    {
        $alumno = session('alumno');
        if (!$alumno) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero.');
        }

        return view('alumno.evaluacion.index', compact('alumno'));
    }
}
