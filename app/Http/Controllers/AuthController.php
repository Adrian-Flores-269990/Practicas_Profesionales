<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use App\Models\Empleado;

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

        // ---------------------------
        // Intentar login como Alumno
        // ---------------------------
        $alumno = Alumno::where('correo_electronico', $entrada)
                        ->orWhere('rpe', $entrada)
                        ->orWhere('cve_alumno', $entrada)
                        ->first();

        if ($alumno && Hash::check($password, $alumno->password)) {
            Auth::login($alumno); // Guard default (web)
            return redirect()->intended('/alumno/home');
        }

        // -----------------------------
        // Intentar login como Empleado
        // -----------------------------
        $empleado = Empleado::where('Correo_Electronico', $entrada)
                            ->orWhere('RPE', $entrada)
                            ->first();

        if ($empleado && Hash::check($password, $empleado->Contrasena)) {
            Auth::guard('empleado')->login($empleado); // Guard 'empleado'
            return redirect()->intended('/encargado/home');
        }

        // -----------------------------
        // Si ninguno coincide
        // -----------------------------
        return back()->withErrors([
            'cuenta' => 'Usuario o contraseña incorrectos.',
        ])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        if (Auth::guard('empleado')->check()) {
            Auth::guard('empleado')->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
