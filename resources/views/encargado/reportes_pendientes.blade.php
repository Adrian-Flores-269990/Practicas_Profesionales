@extends('layouts.encargado')

@section('title', 'Reportes Pendientes de Revisión')

@push('styles')
<style>
  .header-gradient {
    background: #000066;
    color: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
  }
  
  .filtros-container {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .tabla-reportes {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  }
  
  .tabla-reportes table {
    margin-bottom: 0;
  }
  
  .tabla-reportes thead {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
  }
  
  .tabla-reportes th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: #495057;
    padding: 1rem;
  }
  
  .tabla-reportes td {
    padding: 1rem;
    vertical-align: middle;
  }
  
  .tabla-reportes tbody tr {
    border-bottom: 1px solid #dee2e6;
    transition: background 0.2s ease;
  }
  
  .tabla-reportes tbody tr:hover {
    background: #f8f9fa;
  }
  
  .alumno-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  
  .alumno-detalles h6 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
  }
  
  .alumno-detalles small {
    color: #6c757d;
    font-size: 0.8rem;
  }
  
  .numero-reporte {
    font-size: 1.5rem;
    font-weight: bold;
    color: #384daaff;
    text-align: center;
  }
  
  .btn-action {
    padding: 0.375rem 1rem;
    font-size: 0.875rem;
    border-radius: 6px;
  }
  
  .estadisticas-header {
    display: flex;
    gap: 2rem;
    padding: 1rem 0;
  }
  
  .estadistica-item {
    text-align: center;
  }
  
  .estadistica-numero {
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1;
  }
  
  .estadistica-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 0.5rem;
  }
  
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
  }
  
  .empty-state i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
  }
  
  .badge-tipo {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
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
  font-size: 1.2rem; /* Opcional: asegura tamaño consistente */
}

.search-input {
  padding-left: 40px !important; /* Asegura espacio para la lupa */
  height: 48px; /* Coincide con diseño del otro buscador */
  border-radius: 8px;
}

</style>
@endpush

@section('content')
<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    REPORTES PENDIENTES DE REVISIÓN
  </h4>
  
  <div class="bg-white p-4 rounded shadow-sm">
    
    {{-- Header con estadísticas --}}
    <div class="header-gradient">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-2">
            <i class="bi bi-clipboard-check me-2"></i>
            Panel de Revisión de Reportes
          </h4>
          <p class="mb-0 opacity-75">
            Revisa y califica los reportes mensuales de los alumnos
          </p>
        </div>
        <div class="estadisticas-header">
          <div class="estadistica-item">
            <div class="estadistica-numero">{{ $reportes->count() }}</div>
            <div class="estadistica-label">Pendientes</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Filtros de búsqueda (DISEÑO IGUAL AL SEGUNDO) --}}
    <div class="filter-section">

      <div class="row g-3 mb-3">
        <div class="col-md-12">
          <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input
              type="text"
              class="form-control search-input"
              placeholder="Buscar por nombre o clave del alumno..."
              id="filtroAlumno"
              onkeyup="filtrarTabla()">
          </div>
        </div>
      </div>

      <div class="row g-3 align-items-center">

        <div class="col-md-4">
          <select class="form-select" id="filterEstado" onchange="filtrarTabla()">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendientes</option>
            <option value="aprobada">Aprobados</option>
            <option value="rechazada">Rechazados</option>
          </select>
        </div>

        <div class="col-md-4">
          <select class="form-select" id="filtroCarrera" onchange="filtrarTabla()">
            <option value="">Todas las carreras</option>
            @php
              $carreras = $reportes->pluck('carrera')->unique()->sort();
            @endphp
            @foreach($carreras as $carrera)
              <option value="{{ $carrera }}">{{ $carrera }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <div class="fecha-container">
            <select class="form-select" id="filterFechaOpcion">
              <option value="todas" selected>Todas las fechas</option>
              <option value="seleccionar">Elegir fecha...</option>
            </select>

            <input
              type="date"
              class="form-control"
              id="filterFecha"
              style="display:none;">
          </div>
        </div>

      </div>
    </div>


    {{-- Tabla de reportes --}}
    @if($reportes->count() > 0)
      <div class="tabla-reportes">
        <table class="table table-hover" id="tablaReportes">
          <thead>
            <tr>
              <th style="width: 80px;">Reporte</th>
              <th>Alumno</th>
              <th style="width: 150px;">Periodo</th>
              <th style="width: 100px;" class="text-center">Tipo</th>
              <th style="width: 100px;" class="text-center">Archivo</th>
              <th style="width: 200px;" class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reportes as $reporte)
              <tr data-alumno="{{ strtolower($reporte['nombre_alumno']) }} {{ strtolower($reporte['clave_alumno']) }}" 
                  data-carrera="{{ $reporte['carrera'] }}"
                  data-tipo="{{ $reporte['reporte_final'] ? 'final' : 'parcial' }}">
                <td>
                  <div class="numero-reporte">#{{ $reporte['numero_reporte'] }}</div>
                </td>
                
                <td>
                  <div class="alumno-info">
                    <div >
                    </div>
                    <div class="alumno-detalles">
                      <h6>{{ $reporte['nombre_alumno'] }}</h6>
                      <small>
                        <i class="bi bi-key me-1"></i>{{ $reporte['clave_alumno'] }}
                        <span class="ms-2"><i class="bi bi-mortarboard me-1"></i>{{ $reporte['carrera'] }}</span>
                      </small>
                    </div>
                  </div>
                </td>
                
                <td>
                  <div class="small">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ \Carbon\Carbon::parse($reporte['periodo_ini'])->format('d/m/Y') }}
                  </div>
                  <div class="small text-muted">
                    hasta {{ \Carbon\Carbon::parse($reporte['periodo_fin'])->format('d/m/Y') }}
                  </div>
                </td>
                
                <td class="text-center">
                  @if($reporte['reporte_final'])
                    <span class="badge badge-tipo bg-info">
                      <i class="bi bi-star-fill me-1"></i>Final
                    </span>
                  @else
                    <span class="badge badge-tipo bg-secondary">Parcial</span>
                  @endif
                </td>
                
                <td class="text-center">
                  @if($reporte['archivo_agregado'])
                    <span class="badge bg-success">
                      <i class="bi bi-file-earmark-pdf"></i>
                    </span>
                  @else
                    <span class="badge bg-secondary">
                      <i class="bi bi-file-earmark-x"></i>
                    </span>
                  @endif
                </td>
                
                <td>
                  <div class="d-flex gap-2 justify-content-center">
                    <button 
                      class="btn btn-primary btn-action"
                      onclick="revisarReporte({{ $reporte['id_reporte'] }})"
                      title="Revisar y calificar"
                    >
                      <i class="bi bi-clipboard-check me-1"></i>Revisar
                    </button>
                    
                    @if($reporte['archivo_agregado'])
                      <a 
                        href="{{ route('encargado.reportes.descargar', $reporte['id_reporte']) }}"
                        class="btn btn-outline-danger btn-action"
                        target="_blank"
                        title="Descargar PDF"
                      >
                        <i class="bi bi-download"></i>
                      </a>
                    @endif
                    
                    <a 
                      href="{{ route('encargado.reportes_alumno', ['clave' => $reporte['clave_alumno']]) }}"
                      class="btn btn-outline-secondary btn-action"
                      title="Ver todos los reportes del alumno"
                    >
                      <i class="bi bi-person-lines-fill"></i>
                    </a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <div class="mt-3 text-muted small">
        <i class="bi bi-info-circle me-2"></i>
        Mostrando {{ $reportes->count() }} reportes pendientes de revisión
      </div>
    @else
      <div class="empty-state">
        <i class="bi bi-check-circle"></i>
        <h5>¡Todo al día!</h5>
        <p class="text-muted">No hay reportes pendientes de revisión en este momento.</p>
      </div>
    @endif

  </div>
</div>

{{-- Modal para revisar reporte --}}
<div class="modal fade" id="modalRevisarReporte" tabindex="-1" aria-labelledby="modalRevisarReporteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: #000066; color: white;">
        <h5 class="modal-title" id="modalRevisarReporteLabel">
          <i class="bi bi-clipboard-check me-2"></i>Revisar Reporte
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalReporteContent">
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function revisarReporte(idReporte) {
  const modal = new bootstrap.Modal(document.getElementById('modalRevisarReporte'));
  const contentDiv = document.getElementById('modalReporteContent');
  
  // Mostrar loading
  contentDiv.innerHTML = `
    <div class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  `;
  
  modal.show();
  
  // Cargar contenido del reporte
  fetch(`/encargado/reportes/${idReporte}/revisar`)
    .then(response => response.text())
    .then(html => {
      contentDiv.innerHTML = html;
    })
    .catch(error => {
      contentDiv.innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Error al cargar el reporte. Por favor, intenta de nuevo.
        </div>
      `;
      console.error('Error:', error);
    });
}

function calificarReporte(idReporte) {
  const calificacion = document.getElementById('calificacion').value;
  const observaciones = document.getElementById('observaciones').value;
  
  if (!calificacion || calificacion < 0 || calificacion > 100) {
    alert('Por favor, ingresa una calificación válida entre 0 y 100');
    return;
  }
  
  const btn = event.target;
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Guardando...';
  
  fetch(`/encargado/reportes/${idReporte}/aprobar`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
      calificacion: calificacion,
      observaciones: observaciones
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
        mostrarModalExito("Reporte calificado exitosamente");
        setTimeout(() => {
            location.reload();
        }, 1800);
    } else {
        mostrarModalError("No se pudo calificar el reporte. Intenta nuevamente.");
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar calificación';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    mostrarModalError("Ocurrió un error inesperado al calificar el reporte.");
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar calificación';
  });
}

function filtrarTabla() {
  const filtroAlumno = document.getElementById('filtroAlumno').value.toLowerCase();
  const filtroCarrera = document.getElementById('filtroCarrera').value;

  const filas = document.querySelectorAll('#tablaReportes tbody tr');
  
  filas.forEach(fila => {
    const alumno = fila.getAttribute('data-alumno');
    const carrera = fila.getAttribute('data-carrera');
    
    let mostrar = true;
    
    if (filtroAlumno && !alumno.includes(filtroAlumno)) {
      mostrar = false;
    }
    
    if (filtroCarrera && carrera !== filtroCarrera) {
      mostrar = false;
    }
    
    fila.style.display = mostrar ? '' : 'none';
  });
}


function limpiarFiltros() {
  document.getElementById('filtroAlumno').value = '';
  document.getElementById('filtroCarrera').value = '';
  filtrarTabla();
}

function mostrarModalExito(mensaje = "Operación realizada correctamente") {
    document.getElementById("mensajeExito").innerText = mensaje;
    const modal = new bootstrap.Modal(document.getElementById('modalExito'));
    modal.show();
}

function mostrarModalError(mensaje = "Ocurrió un error durante la operación") {
    document.getElementById("mensajeError").innerText = mensaje;
    const modal = new bootstrap.Modal(document.getElementById('modalError'));
    modal.show();
}


</script>
@endpush

<!-- Modal de Éxito -->
<div class="modal fade" id="modalExito" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4" style="border-radius: 12px;">
      
      <div class="text-success mb-3" style="font-size: 3rem;">
        <i class="bi bi-check-circle-fill"></i>
      </div>

      <h4 class="fw-bold mb-2">Operación exitosa</h4>
      <p id="mensajeExito" class="mb-3">Reporte calificado exitosamente.</p>
      
      <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
        Aceptar
      </button>
    </div>
  </div>
</div>

<!-- Modal de Error -->
<div class="modal fade" id="modalError" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4" style="border-radius: 12px;">
      
      <div class="text-danger mb-3" style="font-size: 3rem;">
        <i class="bi bi-x-circle-fill"></i>
      </div>

      <h4 class="fw-bold mb-2">Ocurrió un error</h4>
      <p id="mensajeError" class="mb-3">Error al realizar la operación.</p>
      
      <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
        Cerrar
      </button>
    </div>
  </div>
</div>

@endsection
 