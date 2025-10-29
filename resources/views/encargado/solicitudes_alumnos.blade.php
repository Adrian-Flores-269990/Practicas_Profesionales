@extends('layouts.encargado')

@section('title','Gestión de Solicitudes')

@push('styles')
<style>
  .stats-row {
    margin-bottom: 2rem;
  }
  
  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 4px solid;
    transition: all 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  
  .stat-card.pendientes {
    border-left-color: #ffc107;
  }
  
  .stat-card.aprobadas {
    border-left-color: #28a745;
  }
  
  .stat-card.rechazadas {
    border-left-color: #dc3545;
  }
  
  .stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }
  
  .stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 600;
  }
  
  .nav-tabs .nav-link {
    color: #495057;
    font-weight: 600;
    border: none;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
  }
  
  .nav-tabs .nav-link:hover {
    background-color: #f8f9fa;
    border-color: transparent;
  }
  
  .nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px 8px 0 0;
  }
  
  .tab-content {
    background: white;
    border-radius: 0 0 12px 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
  
  .solicitud-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
  }
  
  .solicitud-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
  }
  
  .solicitud-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e9ecef;
  }
  
  .alumno-info {
    flex: 1;
  }
  
  .alumno-nombre {
    font-size: 1.1rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 0.25rem;
  }
  
  .alumno-clave {
    color: #6c757d;
    font-size: 0.9rem;
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
  
  .status-pendiente {
    background-color: #fff3cd;
    color: #856404;
  }
  
  .status-aprobada {
    background-color: #d4edda;
    color: #155724;
  }
  
  .status-rechazada {
    background-color: #f8d7da;
    color: #721c24;
  }
  
  .status-revision {
    background-color: #d1ecf1;
    color: #0c5460;
  }
  
  .solicitud-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
  }
  
  .detail-item {
    display: flex;
    flex-direction: column;
  }
  
  .detail-label {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
  }
  
  .detail-value {
    font-size: 0.95rem;
    color: #212529;
  }
  
  .action-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }
  
  .btn-ver {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
  }
  
  .btn-aprobar {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
  }
  
  .btn-rechazar {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border: none;
  }
  
  .btn-action {
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
  }
  
  .btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    color: white;
  }
  
  .comentario-rechazo {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
  }
  
  .empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
  }
  
  .empty-state i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
  }
  
  .filter-section {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
  }
  
  .search-box {
    position: relative;
  }
  
  .search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
  }
  
  .search-input {
    padding-left: 40px;
    border-radius: 8px;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    GESTIÓN DE SOLICITUDES Y REGISTROS
  </h4>
  
  <div class="p-4">
    


    {{-- Tabs --}}
    <ul class="nav nav-tabs" id="solicitudesTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="solicitudes-tab" data-bs-toggle="tab" data-bs-target="#solicitudes" type="button">
          <i class="bi bi-file-earmark-text me-2"></i>
          Solicitudes (FPP01)
          <span class="badge bg-light text-dark ms-2">{{ count($solicitudes) }}</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="registros-tab" data-bs-toggle="tab" data-bs-target="#registros" type="button">
          <i class="bi bi-clipboard-check me-2"></i>
          Registros
          <span class="badge bg-light text-dark ms-2">{{ count($registros) }}</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="rechazadas-tab" data-bs-toggle="tab" data-bs-target="#rechazadas" type="button">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Rechazadas
          <span class="badge bg-light text-dark ms-2">{{ count($rechazadas) }}</span>
        </button>
      </li>
    </ul>

    <div class="tab-content" id="solicitudesTabContent">
      
      {{-- Tab 1: Solicitudes Pendientes --}}
      <div class="tab-pane fade show active" id="solicitudes" role="tabpanel">
        
        {{-- Filtros --}}
        <div class="filter-section">
          <div class="row g-3 align-items-center">
            <div class="col-md-6">
              <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="form-control search-input" placeholder="Buscar por nombre o clave..." id="searchSolicitudes">
              </div>
            </div>
            <div class="col-md-3">
              <select class="form-select" id="filterCarrera">
                <option value="">Todas las carreras</option>
                <option value="Software">Ing. en Software</option>
                <option value="Civil">Ing. Civil</option>
                <option value="Industrial">Ing. Industrial</option>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-select" id="filterFecha">
                <option value="">Todas las fechas</option>
                <option value="hoy">Hoy</option>
                <option value="semana">Esta semana</option>
                <option value="mes">Este mes</option>
              </select>
            </div>
          </div>
        </div>

        @if(count($solicitudes) > 0)
          @foreach($solicitudes as $solicitud)
            <div class="solicitud-card">
              <div class="solicitud-header">
                <div class="alumno-info">
                  <div class="alumno-nombre">
                    <i class="bi bi-person-circle me-2"></i>
                    {{ $solicitud['alumno_nombre'] }}
                  </div>
                  <div class="alumno-clave">
                    Clave: {{ $solicitud['alumno_clave'] }} | 
                    {{ $solicitud['carrera'] }}
                  </div>
                </div>
                <span class="status-badge status-{{ $solicitud['estado'] }}">
                  @if($solicitud['estado'] == 'pendiente')
                    <i class="bi bi-clock-fill"></i>
                  @elseif($solicitud['estado'] == 'revision')
                    <i class="bi bi-arrow-repeat"></i>
                  @endif
                  {{ ucfirst($solicitud['estado']) }}
                </span>
              </div>

              <div class="solicitud-details">
                <div class="detail-item">
                  <span class="detail-label">Fecha de Solicitud</span>
                  <span class="detail-value">{{ $solicitud['fecha_solicitud'] }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Empresa</span>
                  <span class="detail-value">{{ $solicitud['empresa'] }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Período</span>
                  <span class="detail-value">{{ $solicitud['fecha_inicio'] }} - {{ $solicitud['fecha_termino'] }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tipo</span>
                  <span class="detail-value">{{ $solicitud['tipo'] }}</span>
                </div>
              </div>

              <div class="action-buttons">
                <button class="btn btn-action btn-ver" onclick="verSolicitud('{{ $solicitud['id'] }}')">
                  <i class="bi bi-eye me-1"></i>
                  Ver Detalles
                </button>
                <button class="btn btn-action btn-aprobar" onclick="aprobarSolicitud('{{ $solicitud['id'] }}', '{{ $solicitud['alumno_nombre'] }}')">
                  <i class="bi bi-check-lg me-1"></i>
                  Aprobar
                </button>
                <button class="btn btn-action btn-rechazar" onclick="rechazarSolicitud('{{ $solicitud['id'] }}', '{{ $solicitud['alumno_nombre'] }}')">
                  <i class="bi bi-x-lg me-1"></i>
                  Rechazar
                </button>
              </div>
            </div>
          @endforeach
        @else
          <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h5>No hay solicitudes pendientes</h5>
            <p class="text-muted">Las solicitudes nuevas aparecerán aquí</p>
          </div>
        @endif
      </div>

      {{-- Tab 2: Registros Pendientes --}}
      <div class="tab-pane fade" id="registros" role="tabpanel">
        @if(count($registros) > 0)
          @foreach($registros as $registro)
            <div class="solicitud-card">
              <div class="solicitud-header">
                <div class="alumno-info">
                  <div class="alumno-nombre">
                    <i class="bi bi-person-check me-2"></i>
                    {{ $registro['alumno_nombre'] }}
                  </div>
                  <div class="alumno-clave">
                    Clave: {{ $registro['alumno_clave'] }} | 
                    Solicitud Aprobada: {{ $registro['fecha_aprobacion_solicitud'] }}
                  </div>
                </div>
                <span class="status-badge status-pendiente">
                  <i class="bi bi-clock-fill"></i>
                  Registro Pendiente
                </span>
              </div>

              <div class="solicitud-details">
                <div class="detail-item">
                  <span class="detail-label">Fecha de Registro</span>
                  <span class="detail-value">{{ $registro['fecha_registro'] }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Empresa</span>
                  <span class="detail-value">{{ $registro['empresa'] }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Asesor Interno</span>
                  <span class="detail-value">{{ $registro['asesor_interno'] ?? 'Por asignar' }}</span>
                </div>
              </div>

              <div class="action-buttons">
                <button class="btn btn-action btn-ver" onclick="verRegistro('{{ $registro['id'] }}')">
                  <i class="bi bi-eye me-1"></i>
                  Ver Registro
                </button>
                <button class="btn btn-action btn-aprobar" onclick="aprobarRegistro('{{ $registro['id'] }}', '{{ $registro['alumno_nombre'] }}')">
                  <i class="bi bi-check-lg me-1"></i>
                  Aprobar Registro
                </button>
                <button class="btn btn-action btn-rechazar" onclick="rechazarRegistro('{{ $registro['id'] }}', '{{ $registro['alumno_nombre'] }}')">
                  <i class="bi bi-x-lg me-1"></i>
                  Rechazar
                </button>
              </div>
            </div>
          @endforeach
        @else
          <div class="empty-state">
            <i class="bi bi-clipboard-check"></i>
            <h5>No hay registros pendientes</h5>
            <p class="text-muted">Los alumnos con solicitud aprobada deben completar su registro</p>
          </div>
        @endif
      </div>

      {{-- Tab 3: Rechazadas --}}
      <div class="tab-pane fade" id="rechazadas" role="tabpanel">
        @if(count($rechazadas) > 0)
          @foreach($rechazadas as $rechazada)
            <div class="solicitud-card">
              <div class="solicitud-header">
                <div class="alumno-info">
                  <div class="alumno-nombre">
                    <i class="bi bi-person-x me-2"></i>
                    {{ $rechazada['alumno_nombre'] }}
                  </div>
                  <div class="alumno-clave">
                    Clave: {{ $rechazada['alumno_clave'] }} | 
                    Rechazada: {{ $rechazada['fecha_rechazo'] }}
                  </div>
                </div>
                <span class="status-badge status-rechazada">
                  <i class="bi bi-x-circle-fill"></i>
                  Rechazada
                </span>
              </div>

              @if($rechazada['comentario_rechazo'])
                <div class="comentario-rechazo">
                  <strong><i class="bi bi-chat-left-text me-1"></i> Motivo del rechazo:</strong>
                  <p class="mb-0 mt-2">{{ $rechazada['comentario_rechazo'] }}</p>
                </div>
              @endif

              <div class="action-buttons mt-3">
                <button class="btn btn-action btn-ver" onclick="verRechazada('{{ $rechazada['id'] }}')">
                  <i class="bi bi-eye me-1"></i>
                  Ver Detalles
                </button>
                <small class="text-muted ms-3">
                  <i class="bi bi-info-circle me-1"></i>
                  El alumno puede corregir y volver a enviar
                </small>
              </div>
            </div>
          @endforeach
        @else
          <div class="empty-state">
            <i class="bi bi-check-circle"></i>
            <h5>No hay solicitudes rechazadas</h5>
            <p class="text-muted">¡Todas las solicitudes están aprobadas!</p>
          </div>
        @endif
      </div>

    </div>

  </div>
</div>

{{-- Modal para rechazar con comentario --}}
<div class="modal fade" id="rechazarModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
          <i class="bi bi-x-circle me-2"></i>
          Rechazar Solicitud
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de rechazar la solicitud de:</p>
        <div class="alert alert-warning">
          <strong id="alumnoRechazar"></strong>
        </div>
        <label class="form-label fw-bold">Motivo del rechazo (requerido):</label>
        <textarea class="form-control" id="comentarioRechazo" rows="4" placeholder="Explica el motivo del rechazo para que el alumno pueda corregir..." required></textarea>
        <small class="text-muted mt-2 d-block">
          <i class="bi bi-info-circle me-1"></i>
          El alumno podrá ver este comentario y corregir su solicitud
        </small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" onclick="confirmarRechazo()">
          <i class="bi bi-x-lg me-1"></i>
          Confirmar Rechazo
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modal para aprobar --}}
<div class="modal fade" id="aprobarModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="bi bi-check-circle me-2"></i>
          Aprobar Solicitud
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Confirmar la aprobación de la solicitud de:</p>
        <div class="alert alert-success">
          <strong id="alumnoAprobar"></strong>
        </div>
        <p class="text-muted small mb-0">
          <i class="bi bi-info-circle me-1"></i>
          Al aprobar, el alumno podrá continuar con el siguiente paso (Registro)
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" onclick="confirmarAprobacion()">
          <i class="bi bi-check-lg me-1"></i>
          Confirmar Aprobación
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  let solicitudActual = null;
  let tipoActual = null; // 'solicitud' o 'registro'

  // Ver detalles
  function verSolicitud(id) {
    // TODO: Abrir modal o redirigir a página de detalles
    console.log('Ver solicitud:', id);
    alert('Abriendo detalles de la solicitud...');
  }

  function verRegistro(id) {
    console.log('Ver registro:', id);
    alert('Abriendo detalles del registro...');
  }

  function verRechazada(id) {
    console.log('Ver rechazada:', id);
    alert('Abriendo detalles...');
  }

  // Aprobar
  function aprobarSolicitud(id, nombre) {
    solicitudActual = id;
    tipoActual = 'solicitud';
    document.getElementById('alumnoAprobar').textContent = nombre;
    
    const modal = new bootstrap.Modal(document.getElementById('aprobarModal'));
    modal.show();
  }

  function aprobarRegistro(id, nombre) {
    solicitudActual = id;
    tipoActual = 'registro';
    document.getElementById('alumnoAprobar').textContent = nombre;
    
    const modal = new bootstrap.Modal(document.getElementById('aprobarModal'));
    modal.show();
  }

  function confirmarAprobacion() {
    // TODO: Llamada al backend
    console.log('Aprobando:', tipoActual, solicitudActual);
    
    alert('✅ ' + (tipoActual === 'solicitud' ? 'Solicitud' : 'Registro') + ' aprobada exitosamente');
    
    bootstrap.Modal.getInstance(document.getElementById('aprobarModal')).hide();
    
    // Recargar o actualizar vista
    // window.location.reload();
  }

  // Rechazar
  function rechazarSolicitud(id, nombre) {
    solicitudActual = id;
    tipoActual = 'solicitud';
    document.getElementById('alumnoRechazar').textContent = nombre;
    document.getElementById('comentarioRechazo').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('rechazarModal'));
    modal.show();
  }

  function rechazarRegistro(id, nombre) {
    solicitudActual = id;
    tipoActual = 'registro';
    document.getElementById('alumnoRechazar').textContent = nombre;
    document.getElementById('comentarioRechazo').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('rechazarModal'));
    modal.show();
  }

  function confirmarRechazo() {
    const comentario = document.getElementById('comentarioRechazo').value.trim();
    
    if (!comentario) {
      alert('⚠️ Debes escribir el motivo del rechazo');
      return;
    }
    
    // TODO: Llamada al backend
    console.log('Rechazando:', tipoActual, solicitudActual, comentario);
    
    alert('❌ ' + (tipoActual === 'solicitud' ? 'Solicitud' : 'Registro') + ' rechazada. El alumno podrá corregir y volver a enviar.');
    
    bootstrap.Modal.getInstance(document.getElementById('rechazarModal')).hide();
    
    // Recargar o actualizar vista
    // window.location.reload();
  }

  // Búsqueda
  document.getElementById('searchSolicitudes')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.solicitud-card');
    
    cards.forEach(card => {
      const text = card.textContent.toLowerCase();
      card.style.display = text.includes(searchTerm) ? '' : 'none';
    });
  });
</script>
@endpush