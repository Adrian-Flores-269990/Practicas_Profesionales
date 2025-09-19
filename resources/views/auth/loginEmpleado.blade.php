@extends('layouts.app')

@section('title', 'Login Empleado')

@section('content')
  <div class="auth-wrap">
    <div class="card-auth">
      <h1>Acceso Empleados</h1>

      {{-- Si luego usas un controlador/guard distinto, cambia el action --}}
      <form method="POST" action="#">
        @csrf

        <div class="field">
          <label for="email">Correo institucional</label>
          <input id="email" type="email" name="email" required autofocus>
        </div>

        <div class="field">
          <label for="password">Contraseña</label>
          <input id="password" type="password" name="password" required>
        </div>

        <button type="submit">Ingresar</button>
      </form>

      <div class="links">
        <a href="{{ route('alumno.login') }}">¿Eres alumno? Ir al login de alumnos</a>
        <a href="{{ route('home') }}">Volver al inicio</a>
      </div>
    </div>
  </div>

  <style>
    .auth-wrap{display:flex;justify-content:center;align-items:flex-start;padding:24px}
    .card-auth{
      width:100%;max-width:420px;background:#fff;border-radius:12px;padding:24px;
      box-shadow:0 8px 24px rgba(0,0,0,.12);border:1px solid #e6eaf0
    }
    .card-auth h1{margin:0 0 14px 0;font-size:22px;color:#0b4f87}
    .field{margin-bottom:12px}
    .field label{display:block;font-size:14px;margin-bottom:6px}
    .field input{
      width:100%;padding:10px;border:1px solid #cfd7e3;border-radius:8px;outline:none
    }
    button{
      width:100%;padding:10px;border:none;border-radius:10px;background:#0b78a8;color:#fff;
      font-weight:700;cursor:pointer
    }
    .links{margin-top:12px;display:flex;flex-direction:column;gap:6px}
  </style>
@endsection
