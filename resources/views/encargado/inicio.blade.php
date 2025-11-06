@extends('layouts.encargado')
@section('title', 'Inicio Encargado')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/encargado.css') }}?v={{ filemtime(public_path('css/encargado.css')) }}">
@endpush

@section('content')
@php
    $encargado = session('empleado');
@endphp


<div class="container-xxl encargado-home my-3">
  <div class="row g-3">

    <!-- Columna principal -->
    <div class="col-lg-8">
      <div class="card profile-card">
        <div class="card-body d-flex align-items-center gap-3">



          <div class="flex-grow-1">
            <h3 class="mb-1 nombre">
              {{ $encargado[0]['nombre'] ?? '' }}
            </h3>

            <div class="kv">
              <div class="kv-label">RPE</div>
              <div class="kv-value">{{ $encargado[0]['rpe'] ?? '-' }}</div>
            </div>

            <div class="kv">
              <div class="kv-label">Correo electrónico</div>
              <div class="kv-value">{{ $encargado[0]['correo_electronico'] ?? '-' }}</div>
            </div>

            <div class="kv">
              <div class="kv-label">Teléfono</div>
              <div class="kv-value">{{ $encargado[0]['telefono'] ?? '-' }}</div>
            </div>
          </div>
        </div>

        <div class="card-body pt-0">
          <div class="kv-grid">

            @if (!empty($encargado))
              @foreach ($encargado as $item)
                  @if ($item['carrera'] !== "NULL")
                  <div class="kv">
                    <div class="kv-label">Encargado de la carrera</div>
                    <div class="kv-value">{{ $item['carrera'] ?? '-' }}</div>
                  </div> 
                  @endif
              @endforeach
            @endif
            
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
            <div class="kv-label">Cargo</div>
            <div class="kv-value">{{ $encargado[0]['cargo'] ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection