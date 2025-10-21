@extends('layouts.dsspp')
@section('title', 'Solicitudes de prácticas profesionales')

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

{{-- Barra superior --}}
<nav class="navbar" style="background-color: #000066;">
  <div class="container-fluid justify-content-center">
    <span class="navbar-text text-white mx-auto fw-bold">
      <h4>Solicitudes recibidas por el Departamento de Servicio Social y Prácticas Profesionales</h4>
    </span>
  </div>
</nav>

<div class="container mt-4">
  <table class="table table-striped align-middle shadow-sm">
    <thead class="table-dark text-center">
      <tr>
        <th>Clave</th>
        <th>Alumno</th>
        <th>Carrera</th>
        <th>Ver</th>
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

          {{-- Botón Ver --}}
          <td>
            <a href="{{ route('dsspp.verSolicitud', $solicitud->Id_Solicitud_FPP01) }}"
              class="btn btn-outline-primary btn-sm">
              Revisar
            </a>
          </td>

          {{-- Estado --}}
          <td>
            @if ($solicitud->Estado_Departamento === 'aprobado')
              <span class="badge bg-success">Aprobada</span>
            @elseif ($solicitud->Estado_Departamento === 'rechazado')
              <span class="badge bg-danger">Rechazada</span>
            @else
              <span class="badge bg-warning text-dark">Pendiente</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center text-muted">No hay solicitudes pendientes</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
