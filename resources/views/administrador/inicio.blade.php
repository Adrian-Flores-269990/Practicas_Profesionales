@extends('layouts.administrador')
@section('title','Inicio Administrador')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/administrador.css') }}?v={{ filemtime(public_path('css/administrador.css')) }}">
@endpush

@section('content')
@php
    $administrador = session('empleado');
@endphp


<div class="container-xxl administrador-home my-3">
  <div class="row g-3">

    <!-- Columna principal -->
    <div class="col-lg-8">
      <div class="card profile-card">
        <div class="card-body d-flex align-items-center gap-3">



          <div class="flex-grow-1">
            <h3 class="mb-1 nombre">
              {{ $administrador['nombre'] ?? '' }}
            </h3>

            <div class="kv">
              <div class="kv-label">RPE</div>
              <div class="kv-value">{{ $administrador['rpe'] ?? '-' }}</div>
            </div>

            <div class="kv">
              <div class="kv-label">rol</div>
              <div class="kv-value">{{ $administrador['rol'] ?? '-' }}</div>
            </div>
          </div>
        </div>

        <div class="card-body pt-0">
          <div class="kv-grid">

            <div class="kv">
              <div class="kv-label">Dependencia</div>
              <div class="kv-value">{{ $administrador['dependencia'] ?? '-' }}</div>
            </div>


          </div>
        </div>
      </div>
    </div>

    <!-- Columna lateral (estatus + accesos) -->
<div class="col-lg-4">
      <div class="card status-card mb-3">
        <div class="card-body status-grid">
          <div class="kv kv-status">
            <div class="kv-label">Fecha</div>
            <div class="kv-value">{{ now()->format('d/m/Y') }}</div>
          </div>
          <div class="kv kv-status">
            <div class="kv-label">Rol</div>
            <div class="kv-value"></div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
