@extends('layouts.alumno')

@section('title','SOLICITUD FORMATO FPP01')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
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

  .solicitud-card {
    background: white;
    border: 1px solid #c4c3c3ff;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 3rem;
    transition: all 0.3s ease;
    max-width: 100%;
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
    color: #000066;
    font-size: 0.95rem;
    font-weight: 700;
    background: #f0f4ff;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    display: inline-block;
    margin-top: 0.25rem;
  }

  .version-badge {
    background: #000066;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
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

  .status-aceptada {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }

  .status-rechazada {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }

  .status-pendiente {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
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
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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

  @media (max-width: 768px) {
    .solicitud-header {
      flex-direction: column;
      gap: 1rem;
    }
    
    .solicitud-details {
      grid-template-columns: 1fr;
    }
    
    .btn-actions {
      flex-direction: column;
    }
    
    .btn-ver-detalle {
      width: 100%;
      justify-content: center;
    }
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <!-- Header -->
  <div class="detalle-header">
    <div class="container">
      <h4 class="text-center">
        <i class="bi bi-file-earmark-text me-2"></i>
        HISTORIAL DE SOLICITUDES FPP01
      </h4>
    </div>
  </div>
  
  <div class="container-fluid px-4 my-4">
    @if($solicitudes->isEmpty())
      <!-- Estado vacío -->
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>No tienes solicitudes registradas</h5>
        <p class="text-muted">Aún no has creado ninguna solicitud de prácticas profesionales</p>
      </div>
    @else
      <!-- Cards de solicitudes -->
      @foreach ($solicitudes as $solicitud)
        <div class="solicitud-card">
          <!-- Header del card -->
          <div class="solicitud-header">
            <div class="alumno-info">
              <div class="alumno-nombre">
                <i class="bi bi-file-text me-2"></i>
                Solicitud de Prácticas Profesionales
              </div>
              <div class="alumno-clave">
                Versión {{ $loop->iteration }}
              </div>
            </div>

            @if ($solicitud->Autorizacion === 1)
              <span class="status-badge status-aceptada">
                <i class="bi bi-check-circle-fill"></i>
                Aceptada
              </span>
            @elseif ($solicitud->Autorizacion === 0)
              @if ($solicitud->Estado_Encargado === 'rechazado' || $solicitud->Estado_Departamento === 'rechazado')
                <span class="status-badge status-rechazada">
                  <i class="bi bi-x-circle-fill"></i>
                  Rechazada
                </span>
              @else
                <span class="status-badge status-pendiente">
                  <i class="bi bi-clock-fill"></i>
                  Pendiente
                </span>
              @endif
            @else
              <span class="status-badge status-pendiente">
                <i class="bi bi-clock-fill"></i>
                En Revisión
              </span>
            @endif
          </div>
          
          <!-- Body del card -->
          <div class="solicitud-details">
            <!-- Fecha -->
            <div class="detail-item">
              <span class="detail-label">Fecha de Solicitud</span>
              <span class="detail-value">
                {{ \Carbon\Carbon::parse($solicitud->Fecha_Solicitud)->format('d/m/Y - H:i') }}
              </span>
            </div>
            
            <!-- Proyecto (si existe) -->
            @if($solicitud->Nombre_Proyecto)
              <div class="detail-item">
                <span class="detail-label">Proyecto</span>
                <span class="detail-value">{{ $solicitud->Nombre_Proyecto }}</span>
              </div>
            @endif
          </div>
          
          <!-- Acciones -->
          <div class="col-12">
            <div class="d-flex justify-content-end gap-2 flex-wrap btn-actions mt-2">
              <a href="{{ route('solicitud.show', $solicitud->Id_Solicitud_FPP01) }}" class="btn-ver-detalle">
                <i class="bi bi-eye-fill me-1"></i>
                Ver Detalle
              </a>
            </div>
          </div>
        </div>
      @endforeach
    @endif
  </div>
</div>
@endsection