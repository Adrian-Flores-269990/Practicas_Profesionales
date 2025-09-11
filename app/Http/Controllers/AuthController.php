<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Alumno; // tu modelo Alumno

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'cuenta'   => 'required|string',
            'password' => 'required|string'
        ]);

        $entrada  = $request->cuenta;
        $password = $request->password;

        $alumno = Alumno::where('correo_electronico', $entrada)
                    ->orWhere('rpe', $entrada)
                    ->orWhere('cve_alumno', $entrada)
                    ->first();

        if (!$alumno) {
            return back()->withErrors([
                'cuenta' => 'La cuenta no existe.',
            ])->withInput();
        }

        if (!Hash::check($password, $alumno->password)) {
            return back()->withErrors([
                'password' => 'La contraseña es incorrecta.',
            ])->withInput();
        }

        // Login exitoso
        return redirect()->intended('/alumno/home');
    }
}