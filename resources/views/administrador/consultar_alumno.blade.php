@extends('layouts.administrador')

@section('title','Consultar Alumno')

@push('styles')
<style>
  .search-container {
    max-width: 800px;
    margin: 0 auto;
  }

  .search-box {
    position: relative;
  }

  .search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
  }

  .form-control-search {
    padding-left: 45px;
    height: 50px;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    font-size: 1rem;
  }

  .form-control-search:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
  }

  .alumno-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: white;
  }

  .alumno-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
  }

  .alumno-header {
    background: linear-gradient(135deg, #384daaff 0%, #84a3e2ff 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 8px 8px 0 0;
  }

  .alumno-info {
    padding: 1.5rem;
  }

  .info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .info-value {
    font-size: 1rem;
    color: #212529;
    margin-top: 0.25rem;
  }

  .formularios-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0 0 8px 8px;
  }

  .formulario-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    text-decoration: none;
    color: #212529;
    transition: all 0.2s ease;
    margin-bottom: 0.75rem;
  }

  .formulario-btn:hover {
    background: #0d6efd;
    color: white;
    border-color: #0d6efd;
    transform: translateX(5px);
  }

  .formulario-btn i {
    font-size: 1.2rem;
  }

  .badge-status {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
    border-radius: 12px;
    font-weight: 600;
  }

  .no-results {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
  }

  .no-results i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.3;
  }

  .search-info {
    background: #e7f3ff;
    border-left: 4px solid #0d6efd;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 2rem;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    CONSULTAR ALUMNO
  </h4>

  <div class="bg-white p-4 rounded shadow-sm">

    {{-- Buscador --}}
    <div class="search-container mb-4">
      <div class="search-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Busca por:</strong> Clave única o Apellidos
      </div>

      <form action="{{ route('administrador.consultar_alumno') }}" method="GET">
        <div class="search-box">
          <i class="bi bi-search search-icon"></i>
          <input
            type="text"
            name="busqueda"
            class="form-control form-control-search"
            value="{{ request('busqueda') }}"
          >
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-3 py-2">
          <i class="bi bi-search me-2"></i> Buscar Alumno
        </button>
      </form>
    </div>

    {{-- Resultados --}}
    @if(isset($alumnos))
      @if(count($alumnos) > 0)
        <div class="mt-4">
          <h5 class="mb-3">
            <i class="bi bi-person-check-fill text-primary me-2"></i>
            Resultados encontrados: <span class="badge bg-primary">{{ count($alumnos) }}</span>
          </h5>

          @foreach($alumnos as $alumno)
            <div class="alumno-card mb-4">
              {{-- Header del alumno --}}
              <div class="alumno-header">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h5 class="mb-1">
                      <i class="bi bi-person-circle me-2"></i>
                      {{ $alumno['nombres'] ?? '' }} {{ $alumno['paterno'] ?? '' }} {{ $alumno['materno'] ?? '' }}
                    </h5>
                    <p class="mb-0 opacity-75">
                      <i class="bi bi-mortarboard me-1"></i> {{ $alumno['carrera'] ?? 'N/A' }}
                    </p>
                  </div>
                  <span class="badge bg-light text-dark">
                    Clave: {{ $alumno['cve_uaslp'] ?? 'N/A' }}
                  </span>
                </div>
              </div>

              {{-- Información del alumno --}}
              <div class="alumno-info">
                <div class="row g-3">
                  <div class="col-md-3">
                    <div class="info-label">Semestre</div>
                    <div class="info-value">{{ $alumno['semestre'] ?? 'N/A' }}</div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-label">Créditos</div>
                    <div class="info-value">{{ $alumno['creditos'] ?? 'N/A' }}</div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-label">Correo</div>
                    <div class="info-value">{{ $alumno['correo'] ?? 'N/A' }}</div>
                  </div>
                </div>
              </div>

              {{-- Formularios disponibles --}}
              <div class="formularios-section">
                <h6 class="mb-3 fw-bold">
                  <i class="bi bi-file-earmark-text me-2"></i>
                  Documentos y Formularios
                </h6>

                <div class="row g-2">
                  <div class="col-md-6">
                    <a href="#" class="formulario-btn">
                      <div>
                        <i class="bi bi-file-earmark-check me-2"></i>
                        <strong>Solicitud FPP01</strong>
                      </div>
                      <span class="badge badge-status bg-success">Completado</span>
                    </a>
                  </div>

                  <div class="col-md-6">
                    <a href="#" class="formulario-btn">
                      <div>
                        <i class="bi bi-clipboard-data me-2"></i>
                        <strong>Estado del Alumno</strong>
                      </div>
                      <span class="badge badge-status bg-info">Ver detalles</span>
                    </a>
                  </div>

                  <div class="col-md-6">
                    <a href="#" class="formulario-btn">
                      <div>
                        <i class="bi bi-journal-text me-2"></i>
                        <strong>Registro de Actividades</strong>
                      </div>
                      <span class="badge badge-status bg-warning text-dark">Pendiente</span>
                    </a>
                  </div>

                  <div class="col-md-6">
                    <a href="#" class="formulario-btn">
                      <div>
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>
                        <strong>Reportes Mensuales</strong>
                      </div>
                      <span class="badge badge-status bg-primary">3 reportes</span>
                    </a>
                  </div>

                  <div class="col-md-6">
                    <a href="#" class="formulario-btn">
                      <div>
                        <i class="bi bi-star me-2"></i>
                        <strong>Evaluación de Empresa</strong>
                      </div>
                      <span class="badge badge-status bg-secondary">No iniciado</span>
                    </a>
                  </div>

                  <div class="col-md-6">
                    <a href="#" class="formulario-btn">
                      <div>
                        <i class="bi bi-folder2-open me-2"></i>
                        <strong>Expediente Completo</strong>
                      </div>
                      <i class="bi bi-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @elseif(request('busqueda'))
        {{-- No se encontraron resultados SOLO si hubo búsqueda --}}
        <div class="no-results">
            <i class="bi bi-search"></i>
            <h5>No se encontraron resultados</h5>
            <p>No hay alumnos que coincidan con "<strong>{{ request('busqueda') }}</strong>"</p>
            <p class="text-muted">Intenta con otra clave o apellidos</p>
        </div>
      @endif
    @endif
    </div>
    {{-- BOTONES DE FILTRO POR ESTADO --}}
    <div class="text-center mt-4">
        <a href="{{ route('administrador.filtrar_alumnos', ['tipo' => 'proceso']) }}"
        class="btn btn-warning mx-2">
            <i class="bi bi-hourglass-split"></i> Alumnos en Proceso
        </a>

        <a href="{{ route('administrador.filtrar_alumnos', ['tipo' => 'terminado']) }}"
        class="btn btn-success mx-2">
            <i class="bi bi-check-circle"></i> Alumnos que Terminaron
        </a>
    </div>
</div>

@endsection
