@extends('layouts.encargado')

@section('title','Alumnos en Proceso')

@push('styles')
<style>
  .stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }
  
  .alumno-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    overflow: hidden;
  }
  
  .alumno-card:hover {
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    transform: translateY(-4px);
  }
  
  .alumno-header {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 1.25rem;
    border-bottom: 1px solid #dee2e6;
  }
  
  .alumno-body {
    padding: 1.5rem;
  }
  
  .info-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
  }
  
  .info-item i {
    width: 20px;
    color: #6c757d;
    margin-right: 8px;
  }
  
  .info-label {
    font-weight: 600;
    color: #495057;
    min-width: 80px;
  }
  
  .info-value {
    color: #212529;
  }
  
  .progress-section {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
  }
  
  .progress-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .progress {
    height: 25px;
    border-radius: 12px;
    background-color: #e9ecef;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
  }
  
  .progress-bar {
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    transition: width 0.6s ease;
  }
  
  .steps-container {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
    gap: 0.5rem;
  }
  
  .step-item {
    flex: 1;
    text-align: center;
    position: relative;
  }
  
  .step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-weight: 600;
    border: 3px solid;
    transition: all 0.3s ease;
  }
  
  .step-circle.completed {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
  }
  
  .step-circle.in-progress {
    background-color: #ffc107;
    border-color: #ffc107;
    color: white;
  }
  
  .step-circle.pending {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #6c757d;
  }
  
  .step-label {
    font-size: 0.7rem;
    color: #6c757d;
    font-weight: 500;
  }
  
  .status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .status-badge.excelente {
    background-color: #d4edda;
    color: #155724;
  }
  
  .status-badge.bien {
    background-color: #d1ecf1;
    color: #0c5460;
  }
  
  .status-badge.regular {
    background-color: #fff3cd;
    color: #856404;
  }
  
  .status-badge.atrasado {
    background-color: #f8d7da;
    color: #721c24;
  }
  
  .filter-section {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }
  
  .action-btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease;
  }
  
  .action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
  }
  
  .semaforo-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
  }
  
  .semaforo-excelente {
    background-color: #28a745;
  }
  
  .semaforo-bien {
    background-color: #17a2b8;
  }
  
  .semaforo-regular {
    background-color: #ffc107;
  }
  
  .semaforo-atrasado {
    background-color: #dc3545;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    ALUMNOS EN PROCESO DE PRÁCTICAS PROFESIONALES
  </h4>
  
  <div class="p-4">
    


    {{-- Filtros --}}
    <div class="filter-section">
      <div class="row align-items-center">
        <div class="col-md-4">
          <input type="text" class="form-control" placeholder="🔍 Buscar por nombre o clave..." id="searchInput">
        </div>
        <div class="col-md-3">
          <select class="form-select" id="filterEstado">
            <option value="">Todos los estados</option>
            <option value="excelente">Excelente</option>
            <option value="bien">Bien</option>
            <option value="regular">Regular</option>
            <option value="atrasado">Atrasado</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-select" id="filterCarrera">
            <option value="">Todas las carreras</option>
            <option value="Software">Ing. en Software</option>
            <option value="Civil">Ing. Civil</option>
            <option value="Industrial">Ing. Industrial</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100" onclick="aplicarFiltros()">
            <i class="bi bi-funnel-fill me-2"></i>Filtrar
          </button>
        </div>
      </div>
    </div>

    {{-- Lista de alumnos --}}
    <div class="row g-4">
      @foreach($alumnos as $alumno)
        <div class="col-md-6 col-xl-4">
          <div class="alumno-card">
            
            {{-- Header --}}
            <div class="alumno-header">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h6 class="mb-1 fw-bold">
                    {{ $alumno['nombres'] }} {{ $alumno['paterno'] }}
                  </h6>
                  <small class="text-muted">
                    <i class="bi bi-bookmark-fill me-1"></i>{{ $alumno['cve_uaslp'] }}
                  </small>
                </div>
                <span class="status-badge {{ $alumno['estado'] }}">
                  <span class="semaforo-indicator semaforo-{{ $alumno['estado'] }}"></span>
                  {{ ucfirst($alumno['estado']) }}
                </span>
              </div>
            </div>

            {{-- Body --}}
            <div class="alumno-body">
              
              {{-- Información básica --}}
              <div class="info-item">
                <i class="bi bi-mortarboard-fill"></i>
                <span class="info-label">Carrera:</span>
                <span class="info-value">{{ $alumno['carrera'] }}</span>
              </div>
              
              <div class="info-item">
                <i class="bi bi-book-fill"></i>
                <span class="info-label">Materia:</span>
                <span class="info-value">{{ $alumno['materia'] }}</span>
              </div>
              
              <div class="info-item">
                <i class="bi bi-building"></i>
                <span class="info-label">Área:</span>
                <span class="info-value">{{ $alumno['area'] }}</span>
              </div>

              {{-- Progreso general --}}
              <div class="progress-section">
                <div class="progress-title">
                  Progreso General
                  <span class="float-end">{{ $alumno['progreso'] }}%</span>
                </div>
                <div class="progress">
                  @php
                    $colorClass = 'bg-danger';
                    if ($alumno['progreso'] >= 80) {
                        $colorClass = 'bg-success';
                    } elseif ($alumno['progreso'] >= 50) {
                        $colorClass = 'bg-info';
                    } elseif ($alumno['progreso'] >= 30) {
                        $colorClass = 'bg-warning';
                    }
                  @endphp
                  <div 
                    class="progress-bar {{ $colorClass }}" 
                    style="width: {{ $alumno['progreso'] }}%"
                  >
                    {{ $alumno['progreso'] }}%
                  </div>
                </div>

                {{-- Pasos completados --}}
                <div class="steps-container">
                  <div class="step-item">
                    <div class="step-circle {{ $alumno['pasos']['solicitud'] }}">
                      @if($alumno['pasos']['solicitud'] == 'completed')
                        <i class="bi bi-check-lg"></i>
                      @elseif($alumno['pasos']['solicitud'] == 'in-progress')
                        <i class="bi bi-hourglass-split"></i>
                      @else
                        1
                      @endif
                    </div>
                    <div class="step-label">Solicitud</div>
                  </div>

                  <div class="step-item">
                    <div class="step-circle {{ $alumno['pasos']['registro'] }}">
                      @if($alumno['pasos']['registro'] == 'completed')
                        <i class="bi bi-check-lg"></i>
                      @elseif($alumno['pasos']['registro'] == 'in-progress')
                        <i class="bi bi-hourglass-split"></i>
                      @else
                        2
                      @endif
                    </div>
                    <div class="step-label">Registro</div>
                  </div>

                  <div class="step-item">
                    <div class="step-circle {{ $alumno['pasos']['reportes'] }}">
                      @if($alumno['pasos']['reportes'] == 'completed')
                        <i class="bi bi-check-lg"></i>
                      @elseif($alumno['pasos']['reportes'] == 'in-progress')
                        <i class="bi bi-hourglass-split"></i>
                      @else
                        3
                      @endif
                    </div>
                    <div class="step-label">Reportes</div>
                  </div>

                  <div class="step-item">
                    <div class="step-circle {{ $alumno['pasos']['evaluacion'] }}">
                      @if($alumno['pasos']['evaluacion'] == 'completed')
                        <i class="bi bi-check-lg"></i>
                      @elseif($alumno['pasos']['evaluacion'] == 'in-progress')
                        <i class="bi bi-hourglass-split"></i>
                      @else
                        4
                      @endif
                    </div>
                    <div class="step-label">Evaluación</div>
                  </div>
                </div>
              </div>

              {{-- Acciones --}}
              <div class="d-flex gap-2 mt-3">
                <button class="btn btn-sm btn-outline-primary action-btn flex-fill">
                  <i class="bi bi-eye me-1"></i> Ver Detalles
                </button>
                <button class="btn btn-sm btn-outline-success action-btn flex-fill">
                  <i class="bi bi-chat-dots me-1"></i> Contactar
                </button>
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
  function aplicarFiltros() {
    // Aquí irá la lógica de filtrado cuando conectes con backend
    console.log('Aplicando filtros...');
  }
  
  // Búsqueda en tiempo real (ejemplo)
  document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    // Lógica de búsqueda
  });
</script>
@endpush