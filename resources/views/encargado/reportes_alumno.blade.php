@extends('layouts.encargado')

@section('title', 'Reportes del Alumno')

@push('styles')
<style>
  .reportes-header {
    background: linear-gradient(135deg, #384daaff 0%, #84a3e2ff 100%);
    color: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
  }
  
  .reporte-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    background: white;
    transition: all 0.3s ease;
  }
  
  .reporte-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
  }
  
  .reporte-card.pendiente {
    border-left: 4px solid #ffc107;
  }
  
  .reporte-card.calificado {
    border-left: 4px solid #28a745;
  }
  
  .badge-calificacion {
    font-size: 1.2rem;
    padding: 0.5rem 1rem;
  }
  
  .info-section {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
  }
  
  .btn-revisar {
    background: linear-gradient(135deg, #384daaff 0%, #84a3e2ff 100%);
    border: none;
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 6px;
    transition: all 0.3s ease;
  }
  
  .btn-revisar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(56, 77, 170, 0.3);
    color: white;
  }
  
  .estadisticas {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    margin-bottom: 2rem;
  }
  
  .estadistica-item {
    text-align: center;
    padding: 1rem;
  }
  
  .estadistica-numero {
    font-size: 2rem;
    font-weight: bold;
    color: #384daaff;
  }
  
  .estadistica-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
  }
</style>
@endpush

@section('content')
<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    REPORTES MENSUALES - {{ strtoupper($alumno['nombre_completo']) }}
  </h4>
  
  <div class="bg-white p-4 rounded shadow-sm">
    
    {{-- Información del alumno --}}
    <div class="reportes-header">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-1">
            <i class="bi bi-person-circle me-2"></i>
            {{ $alumno['nombre_completo'] }}
          </h5>
          <p class="mb-0 opacity-75">
            <i class="bi bi-mortarboard me-2"></i>{{ $alumno['carrera'] }}
            <span class="ms-3"><i class="bi bi-key me-2"></i>Clave: {{ $alumno['cve_uaslp'] }}</span>
          </p>
        </div>
        <a href="{{ route('encargado.consultar_alumno') }}" class="btn btn-light">
          <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
      </div>
    </div>

    {{-- Estadísticas --}}
    <div class="estadisticas">
      <div class="row">
        <div class="col-md-4">
          <div class="estadistica-item">
            <div class="estadistica-numero">{{ $reportes->count() }}</div>
            <div class="estadistica-label">Total de Reportes</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="estadistica-item">
            <div class="estadistica-numero text-success">{{ $reportes->whereNotNull('Calificacion')->count() }}</div>
            <div class="estadistica-label">Calificados</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="estadistica-item">
            <div class="estadistica-numero text-warning">{{ $reportes->whereNull('Calificacion')->count() }}</div>
            <div class="estadistica-label">Pendientes</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Listado de reportes --}}
    @if($reportes->count() > 0)
      @foreach($reportes as $reporte)
        <div class="reporte-card {{ $reporte->Calificacion !== null ? 'calificado' : 'pendiente' }}">
          <div class="row align-items-center">
            <div class="col-md-1 text-center">
              <div style="font-size: 2rem; font-weight: bold; color: #384daaff;">
                #{{ $reporte->Numero_Reporte }}
              </div>
            </div>
            
            <div class="col-md-6">
              <h6 class="mb-2">
                <i class="bi bi-calendar-range me-2"></i>
                Periodo: {{ \Carbon\Carbon::parse($reporte->Periodo_Ini)->format('d/m/Y') }} - 
                {{ \Carbon\Carbon::parse($reporte->Periodo_Fin)->format('d/m/Y') }}
              </h6>
              
              <div class="mb-2">
                <strong>Resumen de actividades:</strong>
                <p class="mb-0 text-muted">{{ Str::limit($reporte->Resumen_Actividad, 150) }}</p>
              </div>
              
              <div class="d-flex gap-2 flex-wrap">
                @if($reporte->Reporte_Final)
                  <span class="badge bg-info">
                    <i class="bi bi-star-fill me-1"></i>Reporte Final
                  </span>
                @endif
                
                @if($reporte->Archivo_Agregado)
                  <span class="badge bg-success">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF Adjunto
                  </span>
                @else
                  <span class="badge bg-secondary">
                    <i class="bi bi-file-earmark-x me-1"></i>Sin archivo
                  </span>
                @endif
                
                @if($reporte->Calificacion !== null)
                  <span class="badge bg-success">
                    <i class="bi bi-check-circle me-1"></i>Calificado
                  </span>
                @else
                  <span class="badge bg-warning text-dark">
                    <i class="bi bi-clock me-1"></i>Pendiente de revisión
                  </span>
                @endif
              </div>
            </div>
            
            <div class="col-md-2 text-center">
              @if($reporte->Calificacion !== null)
                <div>
                  <span class="badge badge-calificacion bg-success">
                    {{ $reporte->Calificacion }}
                  </span>
                  <div class="text-muted small">Calificación</div>
                </div>
              @else
                <div class="text-warning">
                  <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                  <div class="small">Sin calificar</div>
                </div>
              @endif
            </div>
            
            <div class="col-md-3 text-end">
              <div class="d-flex flex-column gap-2">
                <button 
                  class="btn btn-revisar"
                  onclick="verDetalleReporte({{ $reporte->Id_Reporte }})"
                >
                  <i class="bi bi-eye me-2"></i>
                  {{ $reporte->Calificacion !== null ? 'Ver detalles' : 'Revisar y calificar' }}
                </button>
                
                @if($reporte->Archivo_Agregado && $reporte->Nombre_Archivo)
                  <a 
                    href="{{ route('encargado.reportes.descargar', $reporte->Id_Reporte) }}" 
                    class="btn btn-outline-danger"
                    target="_blank"
                  >
                    <i class="bi bi-download me-2"></i>Descargar PDF
                  </a>
                @endif
              </div>
            </div>
          </div>
          
          @if($reporte->Observaciones)
            <div class="info-section mt-3">
              <strong><i class="bi bi-chat-left-text me-2"></i>Observaciones del encargado:</strong>
              <p class="mb-0 mt-2">{{ $reporte->Observaciones }}</p>
            </div>
          @endif
        </div>
      @endforeach
    @else
      <div class="alert alert-info text-center">
        <i class="bi bi-info-circle me-2"></i>
        Este alumno aún no ha subido ningún reporte.
      </div>
    @endif

  </div>
</div>

{{-- Modal para revisar/calificar reporte --}}
<div class="modal fade" id="modalRevisarReporte" tabindex="-1" aria-labelledby="modalRevisarReporteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #384daaff 0%, #84a3e2ff 100%); color: white;">
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
function verDetalleReporte(idReporte) {
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
      alert('Reporte calificado exitosamente');
      location.reload();
    } else {
      alert('Error al calificar el reporte');
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar calificación';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al calificar el reporte');
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar calificación';
  });
}
</script>
@endpush

@endsection
