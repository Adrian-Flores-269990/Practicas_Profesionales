@extends('layouts.alumno')

@section('title','SOLICITUD FORMATO FPP01')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color:#000066;">
    HISTORIAL DE SOLICITUDES FPP01
  </h4>

  <div class="container my-4">
    @if($solicitudes->isEmpty())
      <div class="alert alert-info text-center">
        No tienes solicitudes registradas aún.
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-bordered align-middle shadow-sm">
          <thead class="table-dark text-center">
            <tr>
              <th>Versión</th>
              <th>Fecha de Solicitud</th>
              <th>Estatus</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody class="text-center">
            @foreach ($solicitudes as $index => $solicitud)
              <tr>
                <td class="fw-bold">Versión {{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($solicitud->Fecha_Solicitud)->format('d/m/Y') }}</td>

                {{-- Mostrar el estatus con color --}}
                <td>
                    @if ($solicitud->Autorizacion === 1)
                        <span class="badge bg-success">Aceptada</span>
                    @elseif ($solicitud->Autorizacion === 0)
                        @if ($solicitud->Estado_Encargado === 'rechazado')
                          <span class="badge bg-danger">Rechazada por Encargado</span>
                        @endif
                        @if ($solicitud->Estado_Departamento === 'rechazado')
                          <span class="badge bg-danger">Rechazada por Encargado</span>
                        @endif
                    @else
                        <span class="badge bg-warning text-dark">Pendiente</span>
                    @endif
                </td>

                {{-- Botón para ver la solicitud --}}
                <td>
                    <a href="{{ route('solicitud.show', $solicitud->Id_Solicitud_FPP01) }}" class="btn btn-primary btn-sm">Ver</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
