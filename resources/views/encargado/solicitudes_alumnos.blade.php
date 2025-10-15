@extends('layouts.encargado')
@section('title', 'Solicitudes de prácticas')

@section('content')
<style>
  .navbar-nav .nav-link {
    margin-right: 18px;
    border-radius: 5px;
    transition: background-color 0.3s;
    padding-left: 16px;
    padding-right: 16px;
  }
  .navbar-nav .nav-link:hover {
    background-color: #004A98;
  }
</style>

{{-- Segunda navbar --}}
<nav class="navbar" style="background-color: #000066;">
  <div class="container-fluid justify-content-center">
    <span class="navbar-text text-white mx-auto fw-bold">
      <h4>Lista de alumnos que solicitan prácticas profesionales</h4>
    </span>
  </div>
</nav>

<div class="container mt-4">
  <table class="table table-striped align-middle">
    <thead class="table-dark text-center">
      <tr>
        <th>Clave</th>
        <th>Nombre</th>
        <th>Carrera</th>
        <th>Materia</th>
        <th>Solicitud</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody class="text-center">
        @forelse ($solicitudes as $solicitud)
            <tr>
            {{-- Clave --}}
            <td>{{ $solicitud->Clave_Alumno }}</td>

            {{-- Nombre completo --}}
            <td>
                {{ $solicitud->alumno->Nombre ?? '—' }}
                {{ $solicitud->alumno->ApellidoP_Alumno ?? '' }}
                {{ $solicitud->alumno->ApellidoM_Alumno ?? '' }}
            </td>

            {{-- Carrera --}}
            <td>{{ $solicitud->alumno->Carrera ?? '—' }}</td>

            {{-- Materia --}}
            <td>{{ $solicitud->alumno->Clave_Materia ?? '—' }}</td>

            {{-- Botón Ver solicitud --}}
            <td>
                <a href="{{ route('encargado.verSolicitud', $solicitud->Id_Solicitud_FPP01) }}"
                class="btn btn-primary btn-sm">
                Ver solicitud
                </a>
            </td>

            {{-- Estado --}}
            <td>
                @if ($solicitud->Autorizacion === 1)
                <span class="badge bg-success">Aprobada</span>
                @elseif ($solicitud->Autorizacion === 0)
                <span class="badge bg-danger">Rechazada</span>
                @else
                <span class="badge bg-warning text-dark">Pendiente</span>
                @endif
            </td>
            </tr>
        @empty
            <tr>
            <td colspan="6" class="text-center text-muted">No hay solicitudes registradas</td>
            </tr>
        @endforelse
    </tbody>
  </table>
</div>
@endsection
