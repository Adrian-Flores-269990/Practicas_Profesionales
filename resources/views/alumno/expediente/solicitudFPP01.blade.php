@extends('layouts.alumno')
@section('title','SOLICITUD FORMATO FPP01')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
<style>
    .solicitud-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        background: white;
    }
    
    .solicitud-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 3px solid #000066;
        padding: 0.85rem 1.5rem;
    }
    
    .version-badge {
        background: #000066;
        color: white;
        padding: 0.4rem 0.85rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .solicitud-body {
        padding: 1rem 1.5rem;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .info-icon {
        width: 36px;
        height: 36px;
        background: #f0f4ff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000066;
        font-size: 1.1rem;
        margin-right: 0.85rem;
        flex-shrink: 0;
    }
    
    .info-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.15rem;
    }
    
    .info-value {
        color: #212529;
        font-size: 0.95rem;
    }
    
    .status-badge {
        padding: 0.4rem 0.85rem;
        border-radius: 18px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .status-badge i {
        font-size: 0.9rem;
    }
    
    .status-aceptada {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-rechazada {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .status-pendiente {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
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
        transform: translateX(4px);
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .empty-state-icon {
        font-size: 5rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }
    
    .empty-state h5 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .empty-state p {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }
    
    .timeline-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }
    
    .dot-success { background: #28a745; }
    .dot-danger { background: #dc3545; }
    .dot-warning { background: #ffc107; }
    
    @media (max-width: 768px) {
        .solicitud-body {
            padding: 1rem;
        }
        
        .info-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .info-icon {
            margin-bottom: 0.5rem;
        }
        
        .btn-actions {
            flex-direction: column;
            gap: 0.5rem;
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
    <div class="container my-4">
        @if($solicitudes->isEmpty())
            <!-- Estado vacío -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h5>No tienes solicitudes registradas</h5>
                <p>Aún no has creado ninguna solicitud de prácticas profesionales</p>
            </div>
        @else
            <!-- Cards de solicitudes -->
            @foreach ($solicitudes as $solicitud)
                <div class="solicitud-card">
                    <!-- Header del card -->
                    <div class="card-header-custom">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="version-badge">
                                <i class="bi bi-file-text"></i>
                                Versión {{ $loop->iteration }}
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse($solicitud->Fecha_Solicitud)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Body del card -->
                    <div class="solicitud-body">
                        <div class="row g-3">
                            <!-- Fecha -->
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="info-label">Fecha de Solicitud</div>
                                        <div class="info-value">
                                            {{ \Carbon\Carbon::parse($solicitud->Fecha_Solicitud)->format('d/m/Y - H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Estado -->
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="info-label">Estado</div>
                                        <div class="info-value">
                                            @if ($solicitud->Autorizacion === 1)
                                                <span class="status-badge status-aceptada">
                                                    <span class="timeline-dot dot-success"></span>
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Aceptada
                                                </span>
                                            @elseif ($solicitud->Autorizacion === 0)
                                                @if ($solicitud->Estado_Encargado === 'rechazado' || $solicitud->Estado_Departamento === 'rechazado')
                                                    <span class="status-badge status-rechazada">
                                                        <span class="timeline-dot dot-danger"></span>
                                                        <i class="bi bi-x-circle-fill"></i>
                                                        Rechazada
                                                    </span>
                                                @else
                                                    <span class="status-badge status-pendiente">
                                                        <span class="timeline-dot dot-warning"></span>
                                                        <i class="bi bi-clock-fill"></i>
                                                        Pendiente
                                                    </span>
                                                @endif
                                            @else
                                                <span class="status-badge status-pendiente">
                                                    <span class="timeline-dot dot-warning"></span>
                                                    <i class="bi bi-clock-fill"></i>
                                                    En Revisión
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Proyecto (si existe) -->
                            @if($solicitud->Nombre_Proyecto)
                            <div class="col-12">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="bi bi-briefcase"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="info-label">Proyecto</div>
                                        <div class="info-value">{{ $solicitud->Nombre_Proyecto }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Acciones -->
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 flex-wrap btn-actions mt-2">
                                    <a href="{{ route('solicitud.show', $solicitud->Id_Solicitud_FPP01) }}" class="btn-ver-detalle">
                                        <i class="bi bi-eye"></i>
                                        Ver Detalle
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection