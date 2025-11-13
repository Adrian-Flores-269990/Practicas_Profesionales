@extends('layouts.administrador')

@section('title','Constancias de Validación')

@push('styles')
<style>
  .search-box { position: relative; }
  .search-icon {
    position: absolute; left: 15px; top: 50%;
    transform: translateY(-50%); color: #6c757d;
  }
  .form-control-search {
    padding-left: 45px; height: 48px; border-radius: 8px;
  }

  .alumno-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: white;
    transition: 0.3s;
  }
  .alumno-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
  }
  .alumno-header {
    background: linear-gradient(135deg, #2b4c85 0%, #6fa3ef 100%);
    color: white;
    padding: 1rem;
    border-radius: 8px 8px 0 0;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color:#000066;">
    CONSTANCIAS DE VALIDACIÓN
  </h4>

  <div class="bg-white p-4 rounded shadow-sm">

    {{-- BUSCADOR --}}
    <form action="{{ route('administrador.constancias') }}" method="GET">
      <div class="row g-3 align-items-end">

        {{-- Input texto --}}
        <div class="col-md-5">
          <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input type="text" name="busqueda" class="form-control form-control-search"
            placeholder="Buscar por clave o nombre..."
            value="{{ request('busqueda') }}">
          </div>
        </div>

        {{-- Fecha inicio --}}
        <div class="col-md-3">
          <label class="form-label">Desde</label>
          <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
        </div>

        {{-- Fecha fin --}}
        <div class="col-md-3">
          <label class="form-label">Hasta</label>
          <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
        </div>

        {{-- Botón buscar --}}
        <div class="col-md-1">
          <button class="btn btn-primary w-100" style="height:48px">
            <i class="bi bi-search"></i>
          </button>
        </div>

      </div>
    </form>

    <hr class="my-4">

    {{-- RESULTADOS --}}
    @if(isset($alumnos))

      @if(count($alumnos) > 0)

        @foreach($alumnos as $a)
        <div class="alumno-card mb-4">

            {{-- HEADER --}}
            <div class="alumno-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                <i class="bi bi-person-circle me-2"></i>
                {{ $a->Nombre }} {{ $a->ApellidoP_Alumno }} {{ $a->ApellidoM_Alumno }}
                </h5>
                <small class="opacity-75">{{ $a->Carrera }}</small>
            </div>
            <span class="badge bg-light text-dark">Clave: {{ $a->Clave_Alumno }}</span>
            </div>

            {{-- INFO --}}
            <div class="p-3">
            <div class="row">
                <div class="col-md-4">
                <strong>Correo:</strong> {{ $a->CorreoElectronico ?? 'N/A' }}
                </div>
                <div class="col-md-4">
                <strong>Estado:</strong>
                @php
                    $estado = optional($a->estados->first())->estado;
                @endphp
                {{ $estado ?? 'No registrado' }}
                </div>
                <div class="col-md-4 text-end">

                @php
                    $constancia = $a->estados->firstWhere('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES');
                @endphp

                @if($constancia && $constancia->archivo)
                    <a href="{{ asset('storage/expedientes/constancia-validacion/'.$constancia->archivo) }}"
                    target="_blank"
                    class="btn btn-success">
                    <i class="bi bi-file-earmark-pdf"></i> Ver Constancia
                    </a>
                @else
                    <span class="text-danger"><i class="bi bi-x-circle"></i> Sin constancia</span>
                @endif

                </div>
            </div>
            </div>

        </div>
        @endforeach
      @else
        <div class="text-center py-5 text-muted">
          <i class="bi bi-file-earmark-x" style="font-size:40px"></i>
          <h5 class="mt-3">No se encontraron alumnos finalizados</h5>
        </div>
      @endif

    @endif

  </div>
</div>

@endsection