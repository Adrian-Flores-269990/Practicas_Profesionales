@extends('layouts.administrador')
@section('title', 'Administración de Roles')

@section('content')
<div class="container mt-4">
  <h3 class="mb-4 text-center fw-bold text-primary">Panel de Administración de Roles</h3>

  {{-- Mensaje de éxito --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Tabla de empleados --}}
  <table class="table table-bordered table-hover text-center align-middle shadow-sm">
    <thead class="table-dark">
      <tr>
        <th>RPE</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Cargo</th>
        <th>Área</th>
        <th>Carrera</th>
        <th>Rol Actual</th>
        <th>Cambiar Rol</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($empleados as $empleado)
      <tr>
        {{-- 🔹 RPE --}}
        <td>{{ $empleado->RPE ?? '—' }}</td>

        {{-- 🔹 Nombre completo --}}
        <td>{{ $empleado->Nombre }}</td>

        {{-- 🔹 Correo electrónico --}}
        <td>{{ $empleado->Correo ?? $empleado->Correo_Electronico ?? 'No registrado' }}</td>

        {{-- 🔹 Cargo --}}
        <td>{{ $empleado->Cargo ?? '—' }}</td>

        {{-- 🔹 Área --}}
        <td>{{ $empleado->Area ?? '—' }}</td>

        {{-- 🔹 Carrera --}}
        <td>{{ $empleado->Carrera ?? '—' }}</td>

        {{-- 🔹 Rol actual --}}
        <td>
          <span class="badge bg-primary">
            {{ $empleado->rol->nombre ?? 'Sin rol' }}
          </span>
        </td>

        {{-- 🔹 Cambiar rol --}}
        <td>
          <form action="{{ route('administrador.actualizarRol', $empleado->Id_Encargado) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="d-flex justify-content-center align-items-center gap-2">
              <select name="Id_Rol" class="form-select w-auto">
                @foreach ($roles as $rol)
                  <option value="{{ $rol->id }}" {{ $empleado->Id_Rol == $rol->id ? 'selected' : '' }}>
                    {{ $rol->nombre }}
                  </option>
                @endforeach
              </select>

              <button type="submit" class="btn btn-sm btn-success">Guardar</button>
            </div>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
