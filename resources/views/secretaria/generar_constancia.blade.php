@extends('layouts.secretaria')

@section('title','Generar Constancias')

@push('styles')
<style>
  .stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    text-align: center;
  }
  
  .stat-number {
    font-size: 2rem;
    font-weight: 700;
  }
  
  .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-top: 0.5rem;
  }
  
  .filter-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }
  
  .table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
  }
  
  .custom-table thead {
    background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
  }
  
  .custom-table thead th {
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: #3f51b5;
    border-bottom: 2px solid #3f51b5;
    padding: 1rem;
  }
  
  .custom-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
  }
  
  .custom-table tbody tr:hover {
    background-color: #f8f9fa;
  }
  
  .custom-table tbody td {
    padding: 1rem;
    vertical-align: middle;
  }
  
  .status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
  }
  
  .status-pendiente {
    background-color: #fff3cd;
    color: #856404;
  }
  
  .status-listo {
    background-color: #d4edda;
    color: #155724;
  }
  
  .btn-generar {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  
  .btn-generar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(17,153,142,0.4);
    color: white;
  }
  
  .btn-generar:disabled {
    background: #6c757d;
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .btn-generar.generada {
    background: #28a745;
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
  
  .alert-info-custom {
    background: #e7f3ff;
    border-left: 4px solid #2196f3;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
  }
  
  .no-data {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
  }
  
  .no-data i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    GENERAR CONSTANCIAS DE PRÁCTICAS PROFESIONALES
  </h4>
  
  <div class="p-4">
    


    {{-- Información --}}
    <div class="alert-info-custom">
      <i class="bi bi-info-circle-fill me-2"></i>
      <strong>Instrucciones:</strong> Haz clic en "Generar Constancia" para crear el documento oficial de finalización de prácticas profesionales. Una vez generada, aparecerá en la sección de "Consultar Constancias".
    </div>

    {{-- Filtros --}}
    <div class="filter-card">
      <div class="row align-items-center g-3">
        <div class="col-md-6">
          <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input 
              type="text" 
              class="form-control search-input" 
              placeholder="Buscar por clave o nombre del alumno..."
              id="searchInput"
            >
          </div>
        </div>
        <div class="col-md-3">
          <select class="form-select" id="filterEstado">
            <option value="">Todos</option>
            <option value="pendiente">Pendientes</option>
            <option value="generada">Generadas</option>
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
      </div>
    </div>

    {{-- Tabla de alumnos --}}
    @if(count($alumnos) > 0)
      <div class="table-container">
        <table class="table custom-table mb-0">
          <thead>
            <tr>
              <th>Clave</th>
              <th>Nombre Completo</th>
              <th>Carrera</th>
              <th>Fecha Término</th>
              <th>Estado</th>
              <th class="text-center">Acción</th>
            </tr>
          </thead>
          <tbody>
            @foreach($alumnos as $alumno)
              <tr data-alumno-id="{{ $alumno['clave'] }}">
                <td>
                  <strong class="text-primary">{{ $alumno['clave'] }}</strong>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                         style="width: 35px; height: 35px; font-weight: 600; font-size: 0.9rem;">
                      {{ strtoupper(substr($alumno['nombre'], 0, 1)) }}
                    </div>
                    <div>
                      <div class="fw-semibold">{{ $alumno['nombre'] }}</div>
                      <small class="text-muted">{{ $alumno['correo'] ?? 'N/A' }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <i class="bi bi-mortarboard-fill text-primary me-1"></i>
                  {{ $alumno['carrera'] }}
                </td>
                <td>
                  <i class="bi bi-calendar-check me-1"></i>
                  {{ $alumno['fecha_termino'] }}
                </td>
                <td>
                  @if($alumno['constancia_generada'])
                    <span class="status-badge status-listo">
                      <i class="bi bi-check-circle-fill me-1"></i>
                      Generada
                    </span>
                  @else
                    <span class="status-badge status-pendiente">
                      <i class="bi bi-clock-fill me-1"></i>
                      Pendiente
                    </span>
                  @endif
                </td>
                <td class="text-center">
                  @if($alumno['constancia_generada'])
                    <button 
                      class="btn btn-generar generada btn-sm" 
                      disabled
                    >
                      <i class="bi bi-check-lg me-1"></i>
                      Ya Generada
                    </button>
                  @else
                    <button 
                      class="btn btn-generar btn-sm" 
                    >
                      <i class="bi bi-file-earmark-text me-1"></i>
                      Generar Constancia
                    </button>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="table-container">
        <div class="no-data">
          <i class="bi bi-inbox"></i>
          <h5>No hay alumnos disponibles</h5>
          <p class="text-muted">Los alumnos que terminen sus prácticas profesionales aparecerán aquí</p>
        </div>
      </div>
    @endif

  </div>
</div>

{{-- Modal de confirmación --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="bi bi-file-earmark-check me-2"></i>
          Confirmar Generación de Constancia
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de generar la constancia para el alumno:</p>
        <div class="alert alert-info">
          <strong id="alumnoNombre"></strong><br>
          <small>Clave: <span id="alumnoClave"></span></small>
        </div>
        <p class="text-muted small mb-0">
          <i class="bi bi-info-circle me-1"></i>
          Esta acción registrará la constancia en el sistema.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="confirmarGeneracion()">
          <i class="bi bi-check-lg me-1"></i>
          Confirmar y Generar
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  let claveSeleccionada = null;

  // Búsqueda en tiempo real
  document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.custom-table tbody tr');
    
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
  });

  // Filtros
  document.getElementById('filterEstado')?.addEventListener('change', aplicarFiltros);
  document.getElementById('filterCarrera')?.addEventListener('change', aplicarFiltros);

  function aplicarFiltros() {
    const estado = document.getElementById('filterEstado').value;
    const carrera = document.getElementById('filterCarrera').value;
    const rows = document.querySelectorAll('.custom-table tbody tr');
    
    rows.forEach(row => {
      let mostrar = true;
      const text = row.textContent;
      
      if (estado === 'pendiente' && !text.includes('Pendiente')) {
        mostrar = false;
      }
      if (estado === 'generada' && !text.includes('Generada')) {
        mostrar = false;
      }
      if (carrera && !text.includes(carrera)) {
        mostrar = false;
      }
      
      row.style.display = mostrar ? '' : 'none';
    });
  }

  // Mostrar modal de confirmación
  function generarConstancia(clave, nombre) {
    claveSeleccionada = clave;
    document.getElementById('alumnoClave').textContent = clave;
    document.getElementById('alumnoNombre').textContent = nombre;
    
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
  }

  // Confirmar generación (aquí irá la llamada al backend)
  function confirmarGeneracion() {
    // TODO: Aquí irá la llamada AJAX al backend cuando lo implementes
    // Por ahora solo simulamos la generación
    
    console.log('Generando constancia para clave:', claveSeleccionada);
    
    // Simular éxito
    const row = document.querySelector(`tr[data-alumno-id="${claveSeleccionada}"]`);
    if (row) {
      // Cambiar el estado visualmente
      const badge = row.querySelector('.status-badge');
      badge.className = 'status-badge status-listo';
      badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Generada';
      
      const btnCell = row.querySelector('td:last-child');
      btnCell.innerHTML = `
        <button class="btn btn-generar generada btn-sm" disabled>
          <i class="bi bi-check-lg me-1"></i>
          Ya Generada
        </button>
      `;
    }
    
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
    modal.hide();
    
    // Mostrar mensaje de éxito
    alert('✅ Constancia generada exitosamente');
    
    // TODO: Aquí puedes redirigir o recargar la página
    // window.location.reload();
  }
</script>
@endpush