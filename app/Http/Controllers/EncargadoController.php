<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Encargado;
use Illuminate\Support\Facades\Hash;

class EncargadoController extends Controller
{
    public function showLogin()
    {
        return view('encargado.login'); // la vista de login
    }

    public function login(Request $request)
    {
        $request->validate([
            'rpe' => 'required|integer',
            'contrasena' => 'required|string',
        ]);

        $entrada  = $request->rpe;
        $password = $request->contrasena;

        $encargado = Encargado::where('RPE', $request->rpe)->first();

        if (!$encargado) {
            return back()->withErrors([
                'rpe' => 'No se encontró un encargado con ese RPE.',
            ])->withInput();
        }

        if (!Hash::check($password, $encargado->Contrasena)) {
            return back()->withErrors([
                'contrasena' => 'La contraseña es incorrecta.',
            ])->withInput();
        }

        // Login exitoso: guardar datos en sesión
        session(['encargado' => $encargado]);

        return redirect()->route('encargado.home');
    }

    public function dashboard()
    {
        $encargado = session('encargado');
        return view('encargado.dashboard', compact('encargado'));
    }

    public function logout()
    {
        session()->forget('encargado');
        return redirect()->route('encargado.login');
    }
}
