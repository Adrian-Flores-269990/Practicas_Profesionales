@extends('layouts.alumno')
@section('title','Inicio Alumno')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@endpush

@section('content')
<div class="container-xxl alumno-home my-3">
  <div class="row g-3">

    <!-- Columna principal -->
    <div class="col-lg-8">
      <div class="card profile-card">
        <div class="card-body d-flex align-items-center gap-3">
          <img class="avatar" src="{{ asset('images/perfil.webp') }}" alt="Foto del alumno">
          <div class="flex-grow-1">
            <h3 class="mb-1 nombre">ABARCA CÁRCAMO ALAN YAHIR </h3>

            <div class="kv">
              <div class="kv-label">Clave UASLP</div>
              <div class="kv-value">326769</div>
            </div>

            <div class="kv">
              <div class="kv-label">Carrera</div>
              <div class="kv-value">INGENIERÍA EN SISTEMAS INTELIGENTES</div>
            </div>

          </div>
        </div>

        <div class="card-body pt-0">
          <div class="kv-grid">

            <div class="kv">
              <div class="kv-label">Clave Ingeniería</div>
              <div class="kv-value">202102300073</div>
            </div>

            <div class="kv">
              <div class="kv-label">Asesor</div>
              <div class="kv-value"> HERNANDEZ CASTRO FROYLAN ELOY	</div>
            </div>

            <div class="kv">
              <div class="kv-label">Ciclo escolar</div>
              <div class="kv-value"> 2025-2026	</div>
            </div>

              <div class="kv">
              <div class="kv-label">Semestre</div>
              <div class="kv-value"> 2025-2026/I	</div>
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
            <div class="kv-value">25/09/2025</div>
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
