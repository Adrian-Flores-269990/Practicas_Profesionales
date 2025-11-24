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

  .solicitud-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 3rem;
    transition: all 0.3s ease;
  }


  .solicitud-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid #000066;
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
    background-color: #f3e8ff;
    color: #5a189a;
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
    background: #007bff;
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
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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

  .fecha-container {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .fecha-container select,
  .fecha-container input[type="date"] {
    flex: 1;
  }
  
    .btn-ver-detalle {
        background: #000066;
        color: white;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        font-size: 0.9rem;
    }
    
    .btn-ver-detalle:hover {
        background: #000099;
        color: white;
    }

</style>
@endpush
 
@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    REGISTROS DE PRÁCTICAS PROFESIONALES
  </h4>

  <div class="p-4">

    {{-- Filtros de búsqueda --}}
    <div class="filter-section">

      <div class="row g-3 mb-3">
        <div class="col-md-12">
          <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="form-control search-input" placeholder="Buscar por nombre o clave..." id="searchSolicitudes">
          </div>
        </div>
      </div>

      <div class="row g-3 align-items-center">
        <div class="col-md-4">
          <select class="form-select" id="filterEstado">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendientes</option>
            <option value="aprobada">Aprobados</option>
            <option value="rechazada">Rechazados</option>
        </select>
      </div>

        <div class="col-md-4">
          <select class="form-select" id="filterCarrera">
            <option value="">Todas las carreras</option>
            @foreach($carreras as $carrera)
            <option value="{{ $carrera->Descripcion_Mayúsculas }}">
                {{ $carrera->Descripcion_Mayúsculas }}
            </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <div class="fecha-container">
            <select class="form-select" id="filterFechaOpcion">
              <option value="todas" selected>Todas las fechas</option>
              <option value="seleccionar">Elegir fecha...</option>
            </select>
            <input type="date" class="form-control" id="filterFecha" style="display:none;">
          </div>
        </div>
      </div>
    </div>

    {{-- Lista de registros --}}
    @forelse ($expedientes->reverse() as $expediente)
      <div class="solicitud-card"
      data-estado="{{ is_null($expediente->registro->Autorizacion) ? 'pendiente' : ($expediente->registro->Autorizacion === 1 ? 'aprobada' : 'rechazada') }}"
      data-fecha="{{ $expediente->solicitud->Fecha_Solicitud ? \Carbon\Carbon::parse($expediente->solicitud->Fecha_Solicitud)->format('Y-m-d') : '' }}">

        <div class="solicitud-header">
          <div class="alumno-info">
            <div class="alumno-nombre">
              <i class="bi bi-person-circle me-2"></i>
              {{ $expediente->solicitud->alumno->Nombre ?? '—' }}
              {{ $expediente->solicitud->alumno->ApellidoP_Alumno ?? '' }}
              {{ $expediente->solicitud->alumno->ApellidoM_Alumno ?? '' }}
            </div>
            <div class="alumno-clave">
              Clave: {{ $expediente->solicitud->Clave_Alumno }} |
              {{ $expediente->solicitud->alumno->Carrera ?? '—' }}
            </div>
          </div>

        @if (is_null($expediente->registro->Autorizacion))
            <span class="status-badge status-pendiente">
                <i class="bi bi-clock-fill"></i>
                Pendiente
            </span>
        @elseif ($expediente->registro->Autorizacion === 1)
            <span class="status-badge status-aprobada">
                <i class="bi bi-check-circle-fill"></i>
                Aprobado
            </span>
        @else
            <span class="status-badge status-rechazada">
                <i class="bi bi-x-circle-fill"></i>
                Rechazado
            </span>
        @endif
        </div>

        <div class="solicitud-details">
          <div class="detail-item">
            <span class="detail-label">Materia</span>
            <span class="detail-value">{{ $expediente->solicitud->Materia ?? '—' }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Fecha de Solicitud</span>
            <span class="detail-value">
              {{ $expediente->solicitud->Fecha_Solicitud ? \Carbon\Carbon::parse($expediente->solicitud->Fecha_Solicitud)->format('d/m/Y') : '—' }}
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Periodo</span>
            <span class="detail-value">
              {{ $expediente->solicitud->Fecha_Inicio ? \Carbon\Carbon::parse($expediente->solicitud->Fecha_Inicio)->format('d/m/Y') : '—' }}
              -
              {{ $expediente->solicitud->Fecha_Termino ? \Carbon\Carbon::parse($expediente->solicitud->Fecha_Termino)->format('d/m/Y') : '—' }}
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Créditos</span>
            <span class="detail-value">{{ $expediente->solicitud->Numero_Creditos ?? '—' }}</span>
          </div>
        </div>


        <div class="col-12">
          <div class="d-flex justify-content-end gap-2 flex-wrap btn-actions mt-2">
            <a href="{{ route('encargado.verRegistro', [
              'claveAlumno' => $expediente->solicitud->Clave_Alumno,
              'tipo' => 'Solicitud_FPP02_Firmada',
              'documento' => $expediente->Solicitud_FPP02_Firmada
          ]) }}" class="btn btn-action btn-ver-detalle">
              Ver Solicitud Completa
            </a>
          </div>
        </div>


      </div>
    @empty
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>No hay registros subidos</h5>
        <p class="text-muted">Los registros de los alumnos aparecerán aquí</p>
      </div>
    @endforelse

  </div>
</div>

@endsection

@push('scripts')
<script>
  const searchInput = document.getElementById('searchSolicitudes');
  const estadoSelect = document.getElementById('filterEstado');
  const carreraSelect = document.getElementById('filterCarrera');
  const fechaSelect = document.getElementById('filterFechaOpcion');
  const fechaInput = document.getElementById('filterFecha');
  const cards = document.querySelectorAll('.solicitud-card');

  // Mostrar/ocultar input de fecha
  fechaSelect.addEventListener('change', function() {
    if (this.value === 'seleccionar') {
      fechaInput.style.display = 'block';
    } else {
      fechaInput.style.display = 'none';
      fechaInput.value = '';
      filtrarSolicitudes();
    }
  });

  // Ejecutar filtros combinados en cada cambio
  searchInput.addEventListener('input', filtrarSolicitudes);
  estadoSelect.addEventListener('change', filtrarSolicitudes);
  carreraSelect.addEventListener('change', filtrarSolicitudes);
  fechaInput.addEventListener('change', filtrarSolicitudes);

  function filtrarSolicitudes() {
    const searchTerm = searchInput.value.toLowerCase();
    const estado = estadoSelect.value;
    const carrera = carreraSelect.value.toLowerCase();
    const fecha = fechaInput.value;

    cards.forEach(card => {
      const text = card.textContent.toLowerCase();
      const estadoCard = card.dataset.estado;
      const fechaCard = card.dataset.fecha;
      const carreraCard = text; // Contiene toda la info (nombre + carrera + etc.)

      // Verificar coincidencias
      const coincideTexto = searchTerm === '' || text.includes(searchTerm);
      const coincideEstado = estado === '' || estadoCard === estado;
      const coincideCarrera = carrera === '' || carreraCard.includes(carrera);
      const coincideFecha = fecha === '' || fechaCard === fecha;

      // Mostrar solo si cumple todas las condiciones
      if (coincideTexto && coincideEstado && coincideCarrera && coincideFecha) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  }
</script>
@endpush
