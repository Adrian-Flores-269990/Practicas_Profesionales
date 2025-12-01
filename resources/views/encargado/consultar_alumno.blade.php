@extends('layouts.encargado')

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
    background: #000066;
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
    cursor: pointer;
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
   
  .documentos-expediente {
    display: none;
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #dee2e6;
  }
  
  .documentos-expediente.show {
    display: block;
  }
  
  .semaforo-estado {
    display: none;
    margin-top: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 6px;
    border: 1px solid #dee2e6;
  }
  
  .semaforo-estado.show {
    display: block;
  }
  
  .etapa-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-radius: 6px;
    background: #f8f9fa;
    border-left: 4px solid transparent;
  }
  
  .etapa-item.realizado {
    background: #d4edda;
    border-left-color: #28a745;
  }
  
  .etapa-item.proceso {
    background: #fff3cd;
    border-left-color: #ffc107;
  }
  
  .etapa-item.pendiente {
    background: #f8f9fa;
    border-left-color: #6c757d;
  }
  
  .etapa-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
  }
  
  .etapa-icon.realizado {
    color: #28a745;
  }
  
  .etapa-icon.proceso {
    color: #ffc107;
  }
  
  .etapa-icon.pendiente {
    color: #6c757d;
  }
  
  .etapa-texto {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 500;
  }
  
  .etapa-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
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
        <strong>Busca por:</strong> Clave única con 0 al inicio
      </div>
      
      <form action="{{ route('encargado.consultar_alumno') }}" method="GET">
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
            Solicitud FPP01: <span class="badge bg-primary">{{ count($alumnos) }}</span>
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
                  <div class="col-md-6">
                    <div class="info-label">Correo</div>
                    <div class="info-value">{{ $alumno['correo'] ?? 'N/A' }}</div>
                  </div>
                </div>
                @if(!empty($alumno['solicitud_fpp01']))
                  <hr class="my-4">
                  <div class="row g-3">
                    <div class="col-12">
                      <h6 class="fw-bold mb-2"><i class="bi bi-file-earmark-text me-2"></i>Resumen Solicitud FPP01</h6>
                    </div>
                    <div class="col-md-3">
                      <div class="info-label">ID Solicitud</div>
                      <div class="info-value">{{ $alumno['solicitud_fpp01']['id'] ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-label">Fecha Registro</div>
                      <div class="info-value">{{ $alumno['solicitud_fpp01']['fecha_registro'] ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-label">Empresa</div>
                      <div class="info-value">{{ $alumno['solicitud_fpp01']['empresa'] ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-label">Proyecto</div>
                      <div class="info-value">{{ $alumno['solicitud_fpp01']['proyecto'] ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-label">Horario</div>
                      <div class="info-value">{{ $alumno['solicitud_fpp01']['horario'] ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-label">Estado Encargado</div>
                      <div class="info-value">{{ $alumno['solicitud_fpp01']['estado_encargado'] ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-label">Autorización</div>
                      <div class="info-value">
                        @php
                          $aut = $alumno['solicitud_fpp01']['autorizacion'];
                          $autText = $aut === 1 ? 'Aprobada' : ($aut === 0 ? 'Rechazada' : 'Pendiente');
                        @endphp
                        {{ $autText }}
                      </div>
                    </div>
                  </div>
                @endif
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
                    <div class="formulario-btn" onclick="toggleExpediente('{{ $alumno['cve_uaslp'] ?? '' }}')">
                      <div>
                        <i class="bi bi-folder2-open me-2"></i>
                        <strong>Expediente</strong>
                      </div>
                      <span class="badge badge-status bg-primary">Ver documentos</span>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="formulario-btn" onclick="toggleSemaforo('{{ $alumno['cve_uaslp'] ?? '' }}')">
                      <div>
                        <i class="bi bi-clipboard-data me-2"></i>
                        <strong>Estado del Alumno</strong>
                      </div>
                      <span class="badge badge-status bg-info">Ver detalles</span>
                    </div>
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
                    <a href="{{ route('encargado.reportes_alumno', ['clave' => $alumno['cve_uaslp'] ?? '']) }}" class="formulario-btn">
                      <div>
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>
                        <strong>Reportes Mensuales</strong>
                        @if(isset($alumno['reportes_pendientes']) && $alumno['reportes_pendientes'] > 0)
                          <span class="badge bg-danger ms-2">{{ $alumno['reportes_pendientes'] }} pendientes</span>
                        @endif
                      </div>
                      <span class="badge badge-status bg-primary">{{ $alumno['contador_reportes'] ?? 0 }} reportes</span>
                    </a>
                  </div>
                  
                  <div class="col-md-6">
                      <div class="formulario-btn" onclick="verEvaluacionEmpresa('{{ $alumno['cve_uaslp'] ?? '' }}')">
                      <div>
                        <i class="bi bi-star me-2"></i>
                        <strong>Evaluación de Empresa</strong>
                      </div>
                        @if(isset($alumno['tiene_evaluacion']) && $alumno['tiene_evaluacion'])
                          <span class="badge badge-status bg-success">Completada</span>
                        @else
                          <span class="badge badge-status bg-secondary">No iniciado</span>
                        @endif
                      </div>
                  </div>
                  
                  </div>

                {{-- Documentos del expediente (ocultos por defecto) --}}
                <div id="expediente-{{ $alumno['cve_uaslp'] ?? '' }}" class="documentos-expediente">
                  <h6 class="mb-3">
                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                    Documentos en expediente
                  </h6>
                  @if(isset($documentos) && is_array($documentos) && count($documentos) > 0)
                    <div class="list-group">
                      @foreach($documentos as $doc)
                        <a href="{{ $doc['url'] }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                          <div>
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                            <strong>{{ $doc['titulo'] }}</strong>
                            <span class="text-muted"> — {{ $doc['nombre'] }}</span>
                          </div>
                          <small class="text-muted">{{ $doc['size_kb'] }} KB · {{ $doc['modificado'] }}</small>
                        </a>
                      @endforeach
                    </div>
                  @else
                    <div class="alert alert-light border mb-0">
                      <i class="bi bi-info-circle me-2"></i>
                      No se encontraron documentos en el expediente para esta clave.
                    </div>
                  @endif
                </div>

                {{-- Semáforo de estado (oculto por defecto) --}}
                <div id="semaforo-{{ $alumno['cve_uaslp'] ?? '' }}" class="semaforo-estado">
                  <h6 class="mb-3">
                    <i class="bi bi-traffic-light text-warning me-2"></i>
                    Estado del Proceso de Prácticas Profesionales
                  </h6>
                  @if(!empty($alumno['semaforo']))
                    @foreach($alumno['semaforo'] as $etapa)
                      <div class="etapa-item {{ $etapa['estado'] }}">
                        <div class="etapa-icon {{ $etapa['estado'] }}">
                          @if($etapa['estado'] === 'realizado')
                            <i class="bi bi-check-circle-fill"></i>
                          @elseif($etapa['estado'] === 'proceso')
                            <i class="bi bi-arrow-repeat"></i>
                          @else
                            <i class="bi bi-circle"></i>
                          @endif
                        </div>
                        <div class="etapa-texto">
                          {{ $etapa['etapa'] }}
                        </div>
                        <span class="badge etapa-badge 
                          @if($etapa['estado'] === 'realizado') bg-success
                          @elseif($etapa['estado'] === 'proceso') bg-warning text-dark
                          @else bg-secondary
                          @endif">
                          @if($etapa['estado'] === 'realizado')
                            Completado
                          @elseif($etapa['estado'] === 'proceso')
                            En proceso
                          @else
                            Pendiente
                          @endif
                        </span>
                      </div>
                      @endforeach
                  @else
                    <div class="alert alert-light border mb-0">
                      <i class="bi bi-info-circle me-2"></i>
                      No hay información de seguimiento disponible para este alumno.
                    </div>
                  @endif
                </div>

                  {{-- Evaluación de Empresa (oculto por defecto) --}}
                  <div id="evaluacion-{{ $alumno['cve_uaslp'] ?? '' }}" class="documentos-expediente">
                    <h6 class="mb-3">
                      <i class="bi bi-star-fill text-warning me-2"></i>
                      Evaluación de la Empresa por el Alumno
                    </h6>
                    @if(isset($alumno['evaluacion']) && !empty($alumno['evaluacion']))
                      <div class="alert alert-info mb-3">
                        <strong><i class="bi bi-building me-2"></i>Empresa:</strong> {{ $alumno['evaluacion']['empresa'] ?? 'N/A' }}<br>
                        <strong><i class="bi bi-briefcase me-2"></i>Proyecto:</strong> {{ $alumno['evaluacion']['proyecto'] ?? 'N/A' }}<br>
                      </div>
                    
                      <div class="list-group">
                        @foreach($alumno['evaluacion']['respuestas'] as $respuesta)
                          <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                              <h6 class="mb-2 text-primary">
                                <i class="bi bi-question-circle me-2"></i>{{ $respuesta['pregunta'] }}
                              </h6>
                            </div>
                            <p class="mb-1">
                              <strong>Respuesta:</strong> 
                              <span class="text-dark">{{ $respuesta['respuesta'] }}</span>
                            </p>
                          </div>
                        @endforeach
                      </div>
                    @else
                      <div class="alert alert-light border mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        El alumno aún no ha realizado la evaluación de la empresa.
                      </div>
                    @endif
                  </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="no-results">
          <i class="bi bi-search"></i>
          <h5>No se encontraron alumnos</h5>
          <p class="text-muted">Intenta con otra clave o apellidos</p>
        </div>
      @endif
    @endif

  </div>
</div>

@push('scripts')
<script>
function toggleExpediente(clave) {
  const expedienteDiv = document.getElementById('expediente-' + clave);
  if (expedienteDiv) {
    expedienteDiv.classList.toggle('show');
  }
}

function toggleSemaforo(clave) {
  const semaforoDiv = document.getElementById('semaforo-' + clave);
  if (semaforoDiv) {
    semaforoDiv.classList.toggle('show');
  }
}

  function verEvaluacionEmpresa(clave) {
    const evaluacionDiv = document.getElementById('evaluacion-' + clave);
    if (evaluacionDiv) {
      evaluacionDiv.classList.toggle('show');
    }
  }
</script>
@endpush

@endsection