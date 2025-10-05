@extends('layouts.alumno')
@section('title','Inicio Alumno')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@endpush

@section('content')
@php
  $alumno = session('alumno');
@endphp

<div class="container-xxl alumno-home my-3">
  <div class="row g-3">

    <!-- Columna principal -->
    <div class="col-lg-8">
      <div class="card profile-card">
        <div class="card-body d-flex align-items-center gap-3">
          @php
        $foto = $alumno['url_foto'] ?? null;
        if ($foto && !str_contains($foto, '.jpg') && !str_contains($foto, '.png')) {
            $foto .= '.jpg';
        }
        @endphp

        <img class="avatar"
            src="{{ $foto ?? asset('images/perfil.webp') }}"
            alt="Foto del alumno"
            onerror="this.src='{{ asset('images/perfil.webp') }}'">

          <div class="flex-grow-1">
            <h3 class="mb-1 nombre">
              {{ $alumno['nombres'] ?? '' }} {{ $alumno['paterno'] ?? '' }} {{ $alumno['materno'] ?? '' }}
            </h3>

            <div class="kv">
              <div class="kv-label">Clave UASLP</div>
              <div class="kv-value">{{ $alumno['cve_uaslp'] ?? '-' }}</div>
            </div>

            <div class="kv">
              <div class="kv-label">Carrera</div>
              <div class="kv-value">{{ $alumno['carrera'] ?? '-' }}</div>
            </div>
          </div>
        </div>

        <div class="card-body pt-0">
          <div class="kv-grid">

            <div class="kv">
              <div class="kv-label">Clave de carrera</div>
              <div class="kv-value">{{ $alumno['clave_carrera'] ?? '-' }}</div>
            </div>

            <div class="kv">
              <div class="kv-label">Área</div>
              <div class="kv-value">{{ $alumno['area'] ?? '-' }}</div>
            </div>

            <div class="kv">
              <div class="kv-label">Semestre</div>
              <div class="kv-value">{{ $alumno['semestre'] ?? '-' }}</div>
            </div>

            <div class="kv">
              <div class="kv-label">Créditos</div>
              <div class="kv-value">{{ $alumno['creditos'] ?? '-' }}</div>
            </div>

            <div class="kv">
              <div class="kv-label">Correo electrónico</div>
              <div class="kv-value">{{ $alumno['correo_electronico'] ?? '-' }}</div>
            </div>

            <div class="kv">
              <div class="kv-label">Teléfono celular</div>
              <div class="kv-value">{{ $alumno['telefono_celular'] ?? '-' }}</div>
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
            <div class="kv-label">Condición</div>
            <div class="kv-value">REGULAR</div>
          </div>

          <div class="kv kv-status">
            <div class="kv-label">Situación</div>
            <div class="kv-value">INSCRITO</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
