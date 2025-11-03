@extends('layouts.encargado')

@section('title','Alumnos que Terminaron Prácticas Profesionales')

@push('styles')
<style>
  .header-stats {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }
  
  .stat-item {
    text-align: center;
  }
  
  .stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }
  
  .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
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
  
  .custom-table {
    margin-bottom: 0;
  }
  
  .custom-table thead {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
  }
  
  .custom-table thead th {
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    color: #1976d2;
    border-bottom: 2px solid #1976d2;
    padding: 1rem;
    vertical-align: middle;
  }
  
  .custom-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
  }
  
  .custom-table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }
  
  .custom-table tbody td {
    padding: 1rem;
    vertical-align: middle;
    font-size: 0.9rem;
  }
  
  .estado-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }
  
  .estado-badge.completado {
    background-color: #d4edda;
    color: #155724;
  }
  
  .estado-badge.finalizado {
    background-color: #cce5ff;
    color: #004085;
  }
  
  .estado-badge.aprobado {
    background-color: #d1ecf1;
    color: #0c5460;
  }
  
  .solicitud-badge {
    padding: 0.3rem 0.6rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
  }
  
  .solicitud-aprobada {
    background-color: #28a745;
    color: white;
  }
  
  .solicitud-revision {
    background-color: #ffc107;
    color: #000;
  }
  
  .action-btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
    border-radius: 6px;
    transition: all 0.2s ease;
  }
  
  .action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
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
    border: 1px solid #dee2e6;
  }
  
  .search-input:focus {
    border-color: #1976d2;
    box-shadow: 0 0 0 0.2rem rgba(25,118,210,.25);
  }
  
  .export-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
  }
  
  .export-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102,126,234,0.4);
    color: white;
  }
  
  .pagination {
    margin-top: 1.5rem;
    justify-content: center;
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
    ALUMNOS QUE TERMINARON PRÁCTICAS PROFESIONALES
  </h4>
  
  <div class="p-4">
    


    {{-- Filtros y búsqueda --}}
    <div class="filter-card">
      <div class="row align-items-center g-3">
        <div class="col-md-4">
          <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input 
              type="text" 
              class="form-control search-input" 
              placeholder="Buscar por clave, nombre o carrera..."
              id="searchInput"
            >
          </div>
        </div>
        <div class="col-md-3">
          <select class="form-select" id="filterCarrera">
            <option value="">Todas las carreras</option>
            <option value="Software">Ing. en Software</option>
            <option value="Civil">Ing. Civil</option>
            <option value="Industrial">Ing. Industrial</option>
            <option value="Mecánica">Ing. Mecánica</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-select" id="filterEstado">
            <option value="">Todos los estados</option>
            <option value="Completado">Completado</option>
            <option value="Finalizado">Finalizado</option>
            <option value="Aprobado">Aprobado</option>
          </select>
        </div>

      </div>
    </div>

    {{-- Tabla de alumnos --}}
    @if(count($alumnos) > 0)
      <div class="table-container">
        <table class="table custom-table">
          <thead>
            <tr>
              <th>Clave</th>
              <th>Nombre</th>
              <th>Carrera</th>
              <th>Materia</th>
              <th>Solicitud</th>
              <th>Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($alumnos as $alumno)
              <tr>
                <td>
                  <strong class="text-primary">{{ $alumno['clave'] }}</strong>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                         style="width: 35px; height: 35px; font-weight: 600;">
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
                  <span class="badge bg-secondary">{{ $alumno['materia'] }}</span>
                </td>
                <td>
                  <span class="solicitud-badge {{ $alumno['solicitud'] == 'Aprobada' ? 'solicitud-aprobada' : 'solicitud-revision' }}">
                    @if($alumno['solicitud'] == 'Aprobada')
                      <i class="bi bi-check-circle-fill me-1"></i>
                    @else
                      <i class="bi bi-clock-fill me-1"></i>
                    @endif
                    {{ $alumno['solicitud'] }}
                  </span>
                </td>
                <td>
                  @php
                    $estadoClass = 'completado';
                    if ($alumno['estado'] == 'Finalizado') {
                        $estadoClass = 'finalizado';
                    } elseif ($alumno['estado'] == 'Aprobado') {
                        $estadoClass = 'aprobado';
                    }
                  @endphp
                  <span class="estado-badge {{ $estadoClass }}">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ $alumno['estado'] }}
                  </span>
                </td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary action-btn" title="Ver expediente">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success action-btn" title="Descargar certificado">
                      <i class="bi bi-download"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info action-btn" title="Ver detalles">
                      <i class="bi bi-info-circle"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Paginación (ejemplo) --}}
      <nav>
        <ul class="pagination">
          <li class="page-item disabled">
            <a class="page-link" href="#" tabindex="-1">Anterior</a>
          </li>
          <li class="page-item active"><a class="page-link" href="#">1</a></li>
          <li class="page-item"><a class="page-link" href="#">2</a></li>
          <li class="page-item"><a class="page-link" href="#">3</a></li>
          <li class="page-item">
            <a class="page-link" href="#">Siguiente</a>
          </li>
        </ul>
      </nav>
    @else
      <div class="table-container">
        <div class="no-data">
          <i class="bi bi-inbox"></i>
          <h5>No hay alumnos que hayan terminado</h5>
          <p class="text-muted">Los alumnos que completen sus prácticas profesionales aparecerán aquí</p>
        </div>
      </div>
    @endif

  </div>
</div>

@endsection

@push('scripts')
<script>
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
  document.getElementById('filterCarrera')?.addEventListener('change', aplicarFiltros);
  document.getElementById('filterEstado')?.addEventListener('change', aplicarFiltros);

  function aplicarFiltros() {
    const carrera = document.getElementById('filterCarrera').value;
    const estado = document.getElementById('filterEstado').value;
    const rows = document.querySelectorAll('.custom-table tbody tr');
    
    rows.forEach(row => {
      let mostrar = true;
      
      if (carrera && !row.textContent.includes(carrera)) {
        mostrar = false;
      }
      
      if (estado && !row.textContent.includes(estado)) {
        mostrar = false;
      }
      
      row.style.display = mostrar ? '' : 'none';
    });
  }
</script>
@endpush