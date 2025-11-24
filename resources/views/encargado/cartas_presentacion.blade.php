@extends('layouts.encargado')

@section('title','Cartas de Presentación – Encargado')

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
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;  
  }

  .stat-card.aprobadas {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }

  .stat-card.rechazadas {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
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
    border: 1px solid #c4c3c3ff;
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

  .status-realizado {
    background-color: #d4edda;
    color: #155724;
  }

  .status-rechazado {
    background-color: #f8d7da;
    color: #721c24;
  }

  .status-proceso {
    background-color: #cce5ff;
    color: #004085;
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

  .sin-carta-badge {
    background-color: #f8d7da;
    color: #721c24;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    CARTAS DE PRESENTACIÓN – ENCARGADO
  </h4>

  <div class="p-4">

    {{-- Filtros de búsqueda --}}
    <div class="filter-section">

      <div class="row g-3 mb-3">
        <div class="col-md-12">
          <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="form-control search-input" placeholder="Buscar por nombre o clave..." id="buscar">
          </div>
        </div>
      </div>

      <div class="row g-3 align-items-center">
        <div class="col-md-6">
          <select class="form-select" id="filtroEstado">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendiente</option>
            <option value="realizado">Aprobado</option>
            <option value="rechazado">Rechazado</option>
          </select>
        </div>

        <div class="col-md-6">
          <select class="form-select" id="filtroCarrera">
            <option value="">Todas las carreras</option>
            @foreach(\App\Models\CarreraIngenieria::all() as $c)
              <option value="{{ strtolower($c->Descripcion_Mayúsculas) }}">
                {{ $c->Descripcion_Mayúsculas }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    {{-- Lista de cartas --}}
    @forelse ($alumnos as $a)
      @php 
        $alumno = \App\Models\Alumno::find($a->clave_alumno);

        $sol = \App\Models\SolicitudFPP01::where('Clave_Alumno', $a->clave_alumno)
            ->where('Autorizacion', 1)
            ->first();

        $exp = $sol
            ? \App\Models\Expediente::where('Id_Solicitud_FPP01', $sol->Id_Solicitud_FPP01)->first()
            : null;

        $tieneCarta = $exp && $exp->Carta_Presentacion;

        $estado = strtolower($a->estado);
      @endphp

      <div class="solicitud-card filaCarta"
        data-busqueda="{{ strtolower($alumno->Nombre . ' ' . $alumno->ApellidoP_Alumno . ' ' . $alumno->Clave_Alumno) }}"
        data-estado="{{ $estado }}"
        data-carrera="{{ strtolower($alumno->Carrera) }}">

        <div class="solicitud-header">
          <div class="alumno-info">
            <div class="alumno-nombre">
              <i class="bi bi-person-circle me-2"></i>
              {{ $alumno->Nombre }}
              {{ $alumno->ApellidoP_Alumno }}
            </div>
            <div class="alumno-clave">
              Clave: {{ $alumno->Clave_Alumno }} |
              {{ $alumno->Carrera }}
            </div>
          </div>

          @if($estado === 'pendiente')
            <span class="status-badge status-pendiente">
              <i class="bi bi-clock-fill"></i>
              Pendiente
            </span>
          @elseif($estado === 'realizado')
            <span class="status-badge status-realizado">
              <i class="bi bi-check-circle-fill"></i>
              Aprobado
            </span>
          @elseif($estado === 'rechazado')
            <span class="status-badge status-rechazado">
              <i class="bi bi-x-circle-fill"></i>
              Rechazado
            </span>
          @elseif($estado === 'proceso')
            <span class="status-badge status-proceso">
              <i class="bi bi-arrow-repeat"></i>
              En Proceso
            </span>
          @endif
        </div>

        <div class="solicitud-details">
          <div class="detail-item">
            <span class="detail-label">Fecha de Solicitud</span>
            <span class="detail-value">
              {{ $sol && $sol->Fecha_Solicitud ? \Carbon\Carbon::parse($sol->Fecha_Solicitud)->format('d/m/Y') : '—' }}
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Materia</span>
            <span class="detail-value">{{ $sol->Materia ?? '—' }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Periodo</span>
            <span class="detail-value">
              {{ $sol && $sol->Fecha_Inicio ? \Carbon\Carbon::parse($sol->Fecha_Inicio)->format('d/m/Y') : '—' }}
              -
              {{ $sol && $sol->Fecha_Termino ? \Carbon\Carbon::parse($sol->Fecha_Termino)->format('d/m/Y') : '—' }}
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Créditos</span>
            <span class="detail-value">{{ $sol->Numero_Creditos ?? '—' }}</span>
          </div>
        </div>

        <div class="col-12">
          <div class="d-flex justify-content-end gap-2 flex-wrap btn-actions mt-2">
            @if($tieneCarta)
              <a href="{{ route('encargado.verCartaPresentacion', ['claveAlumno' => $a->clave_alumno]) }}" class="btn-ver-detalle">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i>
                Ver Carta PDF
              </a>
            @else
              <span class="sin-carta-badge">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Sin carta generada
              </span>
            @endif
          </div>
        </div>

      </div>
    @empty
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>No hay cartas de presentación registradas</h5>
        <p class="text-muted">Las cartas de presentación aparecerán aquí</p>
      </div>
    @endforelse

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const buscar = document.getElementById('buscar');
  const filtroEstado = document.getElementById('filtroEstado');
  const filtroCarrera = document.getElementById('filtroCarrera');
  const tarjetas = document.querySelectorAll('.filaCarta');

  function filtrar() {
    const textoBusqueda = buscar.value.toLowerCase();
    const estadoSeleccionado = filtroEstado.value.toLowerCase();
    const carreraSeleccionada = filtroCarrera.value.toLowerCase();

    tarjetas.forEach(tarjeta => {
      const textoTarjeta = tarjeta.dataset.busqueda;
      const estadoTarjeta = tarjeta.dataset.estado;
      const carreraTarjeta = tarjeta.dataset.carrera;

      const coincideBusqueda = textoTarjeta.includes(textoBusqueda);
      const coincideEstado = !estadoSeleccionado || estadoTarjeta === estadoSeleccionado;
      const coincideCarrera = !carreraSeleccionada || carreraTarjeta.includes(carreraSeleccionada);

      if (coincideBusqueda && coincideEstado && coincideCarrera) {
        tarjeta.style.display = '';
      } else {
        tarjeta.style.display = 'none';
      }
    });
  }

  buscar.addEventListener('input', filtrar);
  filtroEstado.addEventListener('change', filtrar);
  filtroCarrera.addEventListener('change', filtrar);
});
</script>

@endsection