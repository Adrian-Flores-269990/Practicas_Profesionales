@extends('layouts.administrador')

@section('title','Autorizaciones Pendientes')

@push('styles')
<style>
  .filter-section {
    background: white;
    padding: 1.25rem;
    border-radius: 10px;
    margin-bottom: 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
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

  .type-badge {
    display:inline-flex; gap:0.5rem; align-items:center; padding:0.35rem 0.75rem; border-radius:999px; font-weight:700; font-size:0.85rem;
  }
  .type-Solicitud { background:#fff3cd; color:#856404; border:1px solid rgba(0,0,0,0.03); }
  .type-Registro { background:#e7f5ff; color:#055160; border:1px solid rgba(0,0,0,0.03); }
  .type-Reporte { background:#e9f7ef; color:#1b6b2b; border:1px solid rgba(0,0,0,0.03); }
  .type-Constancia { background:#f3e8ff; color:#5a189a; border:1px solid rgba(0,0,0,0.03); }

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

  .filters-row {
    display:flex;
    gap:0.75rem;
    align-items:center;
    flex-wrap:wrap;
  }

  .filters-row .form-select, .filters-row .form-control {
    min-width: 160px;
  }

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

  .fecha-container {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .fecha-container select,
  .fecha-container input[type="date"] {
    flex: 1;
  }
</style>
@endpush

@section('content')
<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    AUTORIZACIONES PENDIENTES
  </h4>

  <div class="p-4">

    {{-- FILTROS --}}
    <div class="filter-section">

      {{-- Search + Tipo de Autorización --}}
      <div class="row g-3 mb-3">
        <div class="col-md-12">
          <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input id="searchGlobal" type="text" class="form-control search-input" placeholder="Buscar..." />
          </div>
        </div>
      </div>

      <div class="row g-3 align-items-center">
          <div class="col-md-4">
            <select id="filterTipo" class="form-select">
              <option value="">Tipo las autorizaciones</option>
              <option value="Solicitud">Solicitud</option>
              <option value="Registro">Registro</option>
              <option value="Reporte">Reporte</option>
              <option value="Constancia">Constancia</option>
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
    @php
    use Illuminate\Support\Collection;

    $autorizaciones = collect();

    /* Solicitudes */
    foreach($solicitudes as $s){
        $autorizaciones->push((object)[
            'tipo' => 'Solicitud',
            'fecha' => $s->Fecha_Solicitud,
            'data' => $s
        ]);
    }

    /* Registros */
    foreach($registros as $r){
        if(!$r->solicitud || !$r->solicitud->alumno){
            continue;
        }

        $autorizaciones->push((object)[
            'tipo' => 'Registro',
            'fecha' => $r->registro->Fecha_Asignacion,
            'data' => (object)[
                'solicitud' => $r->solicitud,
                'registro' => $r->registro,
            ]
        ]);
    }

    /* Reportes */
    foreach($reportes as $rep){
        $autorizaciones->push((object)[
            'tipo' => 'Reporte',
            'fecha' => $rep->Fecha_Reporte ?? $rep->created_at,
            'data' => $rep
        ]);
    }

    $autorizaciones = $autorizaciones->sortByDesc('fecha');
    @endphp

    @forelse($autorizaciones as $item)
        @php
            $solicitud = null;
            $reg = null;

            if($item->tipo === 'Registro'){
                $solicitud = $item->data->solicitud ?? null;
                $reg = $item->data->registro ?? null;
            } elseif($item->tipo === 'Solicitud'){
                $solicitud = $item->data;
            } elseif($item->tipo === 'Reporte'){
                $solicitud = $item->data;
            }
        @endphp

        <div class="solicitud-card"
            data-tipo="{{ $item->tipo }}"
            data-fecha="{{ \Carbon\Carbon::parse($item->fecha)->format('Y-m-d') }}"
            data-carrera="{{ $solicitud->alumno->Carrera ?? '' }}"
            data-estado="{{ $item->estado ?? '' }}">

            <div class="solicitud-header">
                <div class="alumno-info">
                    <div class="alumno-nombre">
                        <i class="bi bi-person-circle me-2"></i>
                        {{ $solicitud->alumno->Nombre ?? $solicitud->Nombre ?? '—' }}
                        {{ $solicitud->alumno->ApellidoP_Alumno ?? $solicitud->ApellidoP_Alumno ?? '' }}
                        {{ $solicitud->alumno->ApellidoM_Alumno ?? $solicitud->ApellidoM_Alumno ?? '' }}
                    </div>
                    <div class="alumno-clave">
                        Clave: {{ $solicitud->Clave_Alumno ?? '—' }} |
                        {{ $solicitud->alumno->Carrera ?? '—' }}
                    </div>
                </div>

                <span class="type-badge type-{{ $item->tipo }}">
                    <i class="bi bi-clock"></i>
                    {{ $item->tipo }}
                </span>
            </div>

            <div class="solicitud-details">
                {{-- Materia --}}
                <div class="detail-item">
                    <span class="detail-label">Materia</span>
                    <span class="detail-value">{{ $solicitud->Materia ?? '—' }}</span>
                </div>

                {{-- Fecha dinámica según tipo --}}
                <div class="detail-item">
                    <span class="detail-label">
                        @if($item->tipo === 'Solicitud')
                            Fecha de solicitud
                        @elseif($item->tipo === 'Registro')
                            Fecha de registro
                        @elseif($item->tipo === 'Reporte')
                            Fecha de reporte
                        @elseif($item->tipo === 'Constancia')
                            Fecha de constancia
                        @else
                            Fecha
                        @endif
                    </span>
                    <span class="detail-value">
                        {{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}
                    </span>
                </div>

                {{-- Periodo --}}
                <div class="detail-item">
                    <span class="detail-label">Periodo</span>
                    <span class="detail-value">
                        {{ isset($solicitud->Fecha_Inicio) ? \Carbon\Carbon::parse($solicitud->Fecha_Inicio)->format('d/m/Y') : '—' }}
                        -
                        {{ isset($solicitud->Fecha_Termino) ? \Carbon\Carbon::parse($solicitud->Fecha_Termino)->format('d/m/Y') : '—' }}
                    </span>
                </div>

                {{-- Créditos --}}
                <div class="detail-item">
                    <span class="detail-label">Créditos</span>
                    <span class="detail-value">{{ $solicitud->Numero_Creditos ?? '—' }}</span>
                </div>
            </div>
            <div class="action-buttons">
                <a class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>
                    Autorizar
                </a>
                <a class="btn btn-danger">
                    <i class="bi bi-x-circle me-1"></i>
                    Rechazar
                </a>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h5>No hay autorizaciones pendientes</h5>
        </div>
    @endforelse
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const searchInput = document.getElementById('searchGlobal');
    const tipoSelect = document.getElementById('filterTipo');
    const carreraSelect = document.getElementById('filterCarrera');
    const fechaOpcion = document.getElementById('filterFechaOpcion');
    const fechaInput = document.getElementById('filterFecha');
    const cards = Array.from(document.querySelectorAll('.solicitud-card'));

    // Mostrar u ocultar input de fecha
    fechaOpcion.addEventListener('change', function(){
        if(this.value === 'seleccionar'){
            fechaInput.style.display = 'inline-block';
        } else {
            fechaInput.style.display = 'none';
            fechaInput.value = '';
            filtrar();
        }
    });

    // Agregar listener a todos los filtros
    [searchInput, tipoSelect, carreraSelect, fechaInput].forEach(el => {
        if(el) el.addEventListener('input', filtrar);
        if(el) el.addEventListener('change', filtrar);
    });

    function filtrar(){
        const term = (searchInput.value || '').toLowerCase().trim();
        const tipo = (tipoSelect.value || '').toLowerCase().trim();
        const carrera = (carreraSelect.value || '').toLowerCase().trim();
        const fecha = fechaInput.value;

        cards.forEach(card => {
            const cardText = (card.textContent || '').toLowerCase();
            const cardTipo = (card.dataset.tipo || '').toLowerCase();
            const cardCarrera = (card.dataset.carrera || '').toLowerCase();
            const cardFecha = (card.dataset.fecha || '').toString();

            const matchesTerm = !term || cardText.includes(term);
            const matchesTipo = !tipo || cardTipo === tipo;
            const matchesCarrera = !carrera || cardCarrera.includes(carrera);
            const matchesFecha = !fecha || cardFecha === fecha;

            card.style.display = (matchesTerm && matchesTipo && matchesCarrera && matchesFecha) ? '' : 'none';
        });
    }

    // Filtrar al cargar
    filtrar();
})();
</script>

@endpush
