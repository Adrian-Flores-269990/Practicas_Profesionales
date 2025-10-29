@extends('layouts.alumno')

@section('title', 'Detalle de Solicitud FPP01')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@endpush

@section('content')
<div class="container my-4">
  {{-- Mostrar constancia de vigencia de derechos si existe --}}
  @if (!empty($solicitud->Archivo_CVD))
    <div class="card mb-4">
      <div class="card-header bg-info text-white fw-bold">Constancia de Vigencia de Derechos</div>
      <div class="card-body">
        <a href="{{ asset('storage/expedientes/carta-vigencia-derechos/' . $solicitud->Archivo_CVD) }}" target="_blank" class="btn btn-outline-primary mb-2">
          <i class="bi bi-file-earmark-pdf"></i> Ver PDF
        </a>
        <div class="ratio ratio-16x9">
          <iframe src="{{ asset('storage/expedientes/carta-vigencia-derechos/' . $solicitud->Archivo_CVD) }}" frameborder="0"></iframe>
        </div>
      </div>
    </div>
  @endif

  {{-- Encabezado --}}
  <h4 class="text-center fw-bold text-white py-3 mb-4" style="background-color:#000066;">
    Detalle de la Solicitud FPP01
  </h4>

  {{-- Información general --}}
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="row mb-2">
        <div class="col-md-6">
          <p><strong>Fecha de solicitud:</strong> {{ \Carbon\Carbon::parse($solicitud->Fecha_Solicitud)->format('d/m/Y') }}</p>
        </div>
        <div class="col-md-6">
          <p><strong>Estatus:</strong>
            @if ($solicitud->Autorizacion === 1)
                <span class="badge bg-success">Aceptada</span>
            @elseif ($solicitud->Autorizacion === 0)
                <span class="badge bg-danger">Rechazada</span>
            @else
                <span class="badge bg-warning text-dark">Pendiente</span>
            @endif
          </p>
        </div>
      </div>

      <p><strong>Nombre del proyecto:</strong> {{ $solicitud->Nombre_Proyecto ?? 'No especificado' }}</p>
      <p><strong>Actividades:</strong> {{ $solicitud->Actividades ?? 'No especificadas' }}</p>
      <p><strong>Periodo:</strong>
        {{ \Carbon\Carbon::parse($solicitud->Fecha_Inicio)->format('d/m/Y') }} -
        {{ \Carbon\Carbon::parse($solicitud->Fecha_Termino)->format('d/m/Y') }}
      </p>
    </div>
  </div>

  {{-- Mostrar detalles adicionales según estatus --}}
  @if ($solicitud->Autorizacion === 0)
    {{-- Alerta principal --}}
    <div class="alert alert-danger mt-3">
      <h5 class="fw-bold mb-2"><i class="bi bi-x-circle"></i> Tu solicitud fue rechazada</h5>
      <p class="mb-2">Revisa los comentarios y corrige las secciones marcadas antes de reenviarla.</p>
    </div>

    {{-- Comentarios del encargado --}}
    @if (!empty($solicitud->Comentarios))
      <div class="card border-danger mb-3">
        <div class="card-header bg-danger text-white fw-bold">Comentarios del encargado</div>
        <div class="card-body">
          <p class="mb-0">{{ $solicitud->Comentarios }}</p>
        </div>
      </div>
    @endif

    {{-- Comentarios del encargado --}}
    @if (!empty($solicitud->autorizacion->Comentario_Encargado))
    <div class="card border-danger mb-3">
        <div class="card-header bg-danger text-white fw-bold">Comentario del encargado</div>
        <div class="card-body">
        <p class="mb-0">{{ $solicitud->autorizacion->Comentario_Encargado }}</p>
        </div>
    </div>
    @endif

    {{-- Botón para corregir --}}
    <div class="text-center mb-4">
      <a href="{{ route('alumno.expediente.solicitud.editar', $solicitud->Id_Solicitud_FPP01) }}" class="btn btn-warning fw-bold">
        <i class="bi bi-pencil-square"></i> Corregir y reenviar solicitud
      </a>
    </div>

  @elseif($solicitud->Autorizacion === 1)
    <div class="alert alert-success">
      <h5 class="fw-bold mb-2"><i class="bi bi-check-circle"></i> Tu solicitud fue aprobada</h5>
      <p class="mb-0">El encargado ha aceptado tu solicitud. Puedes continuar con el proceso de prácticas.</p>
    </div>

  @else
    <div class="alert alert-warning">
      <h5 class="fw-bold mb-2"><i class="bi bi-hourglass-split"></i> Tu solicitud está pendiente</h5>
      <p class="mb-0">Aún no ha sido revisada por el encargado. Te notificaremos cuando haya una respuesta.</p>
    </div>
  @endif

  {{-- Botón para regresar --}}
  <div class="text-center mt-4">
    <a href="{{ route('alumno.expediente.solicitudes') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Regresar al historial
    </a>
  </div>
</div>
@endsection
