@extends('layouts.alumno')

@section('title', 'Detalle de Solicitud FPP01')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
<style>
    
    
    .status-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .status-card.success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-left: 5px solid #28a745;
    }
    
    .status-card.danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border-left: 5px solid #dc3545;
    }
    
    .status-card.warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-left: 5px solid #ffc107;
    }
    
    .status-card .card-body {
        padding: 1.5rem;
    }
    
    .status-card h5 {
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .section-card {
        background: white;
        border: none;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    
    .section-header {
        background: linear-gradient(135deg, #000066 0%, #000099 100%);
        color: white;
        padding: 1.25rem 2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.05rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .section-body {
        padding: 2rem;
    }
    
    .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .form-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .form-group {
        flex: 1 1 calc(33.333% - 1rem);
        min-width: 250px;
    }
    
    .form-group.full-width {
        flex: 1 1 100%;
    }
    
    .form-group.half-width {
        flex: 1 1 calc(50% - 0.75rem);
    }
    
    .form-label-custom {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 0.5rem;
    }
    
    .form-label-custom i {
        color: #000066;
        font-size: 1rem;
    }
    
    .form-value {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border-left: 3px solid #000066;
        color: #212529;
        font-size: 0.95rem;
        line-height: 1.5;
        transition: all 0.2s ease;
    }
    
    .form-value:hover {
        background: #e9ecef;
    }
    
    .form-value.empty {
        color: #6c757d;
        font-style: italic;
        border-left-color: #dee2e6;
    }
    
    .subsection-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #000066;
        margin: 2rem 0 1rem 0;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #000066;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .subsection-title i {
        font-size: 1.3rem;
    }
    
    .approval-card {
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: 2px solid;
    }
    
    .approval-card.approved {
        border-color: #28a745;
        background: #f8fff9;
    }
    
    .approval-card.rejected {
        border-color: #dc3545;
        background: #fff8f8;
    }
    
    .approval-card.pending {
        border-color: #ffc107;
        background: #fffef8;
    }
    
    .approval-header {
        padding: 1rem 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .approval-header.approved {
        background: #d4edda;
        color: #155724;
    }
    
    .approval-header.rejected {
        background: #f8d7da;
        color: #721c24;
    }
    
    .approval-header.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .approval-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
    }
    
    .approval-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .approval-body {
        padding: 1.5rem;
    }
    
    .comment-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .comment-text {
        color: #212529;
        line-height: 1.6;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border-left: 4px solid #dc3545;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .pdf-viewer-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        background: white;
    }
    
    .pdf-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 1.25rem 2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .pdf-body {
        padding: 2rem;
    }
    
    .pdf-frame {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        width: 100%;
        height: 500px;
        margin-bottom: 1rem;
    }
    
    .btn-action {
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        text-decoration: none;
    }
    
    .btn-back {
        background: #6c757d;
        color: white;
    }
    
    .btn-back:hover {
        background: #5a6268;
        color: white;
        transform: translateX(-4px);
    }
    
    .btn-edit {
        background: #ffc107;
        color: #000;
    }
    
    .btn-edit:hover {
        background: #ffb300;
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255,193,7,0.3);
    }
    
    .btn-open-pdf {
        background: #17a2b8;
        color: white;
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        text-decoration: none;
    }
    
    .btn-open-pdf:hover {
        background: #138496;
        color: white;
        transform: translateX(4px);
    }
    
    .empty-state {
        text-align: center;
        padding: 2rem;
        background: #fff3cd;
        border-radius: 8px;
        border: 2px dashed #ffc107;
    }
    
    .empty-state i {
        font-size: 2.5rem;
        color: #856404;
        margin-bottom: 0.75rem;
    }
    
    .empty-state p {
        color: #856404;
        margin: 0;
        font-weight: 500;
    }
    
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 12px;
    }
    
    .badge-check {
        background: #28a745;
        color: white;
        padding: 0.4rem 0.85rem;
        border-radius: 16px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .badge-x {
        background: #dc3545;
        color: white;
        padding: 0.4rem 0.85rem;
        border-radius: 16px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .badge-day {
        background: #000066;
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
        margin-right: 0.35rem;
        margin-bottom: 0.35rem;
    }
    
    @media (max-width: 768px) {
        .form-group {
            flex: 1 1 100%;
        }
        
        .section-body {
            padding: 1.25rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-action {
            width: 100%;
            justify-content: center;
        }
        
        .pdf-frame {
            height: 400px;
        }
        
        .approval-header {
            flex-direction: column;
            align-items: flex-start;
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
                DETALLE COMPLETO DE SOLICITUD FPP01
            </h4>
        </div>
    </div>
    
    <div class="container my-4">
        @php
            $alumno = $solicitud->alumno ?? null;
            $dependencia = $solicitud->dependenciaMercadoSolicitud;
            $empresa = optional($dependencia)->dependenciaEmpresa;
            $sectorPrivado = optional($dependencia)->sectorPrivado;
            $sectorPublico = optional($dependencia)->sectorPublico;
            $sectorUaslp = optional($dependencia)->sectorUaslp;
            
            // Obtener datos de la sesión del webservice
            $alumnoSession = session('alumno', []);
            
            // Obtener asesor externo
            $asesorExterno = null;
            if ($solicitud->Clave_Asesor_Externo) {
                $asesorExterno = \App\Models\AsesorExterno::find($solicitud->Clave_Asesor_Externo);
            }
            
            $claveAlumno = $alumno->Clave_Alumno ?? null;
            
            // CORREGIDO: Buscar PDFs específicos de esta solicitud
            $pdfEstadistica = null;
            $pdfVigenciaDerechos = null;
            $pdfCartaPasante = null;

            if ($claveAlumno) {
                // Buscar Estadística General (solo si fue subida)
                if ($solicitud->Estadistica_General == 1) {
                    $filesEstadistica = \Illuminate\Support\Facades\Storage::disk('public')->files('expedientes/estadistica-general');
                    $pdfsEstadistica = collect($filesEstadistica)->filter(fn($f) => str_contains($f, '0'.$claveAlumno))->sortDesc();
                    if ($pdfsEstadistica->count() > 0) {
                        $pdfEstadistica = $pdfsEstadistica->first();
                    }
                }

                // Buscar Constancia Vigencia de Derechos (solo si fue subida)
                if ($solicitud->Constancia_Vig_Der == 1) {
                    $filesVigencia = \Illuminate\Support\Facades\Storage::disk('public')->files('expedientes/carta-vigencia-derechos');
                    $pdfsVigencia = collect($filesVigencia)->filter(fn($f) => str_contains($f, '0'.$claveAlumno))->sortDesc();
                    if ($pdfsVigencia->count() > 0) {
                        $pdfVigenciaDerechos = $pdfsVigencia->first();
                    }
                }

                // Buscar Carta Pasante (solo si fue subida)
                if ($solicitud->Carta_Pasante == 1) {
                    $filesCartaPasante = \Illuminate\Support\Facades\Storage::disk('public')->files('expedientes/carta-pasante');
                    $pdfsCartaPasante = collect($filesCartaPasante)->filter(fn($f) => str_contains($f, '0'.$claveAlumno))->sortDesc();
                    if ($pdfsCartaPasante->count() > 0) {
                        $pdfCartaPasante = $pdfsCartaPasante->first();
                    }
                }
            }
        @endphp

        <!-- Status Card Principal -->
        @if ($solicitud->Autorizacion === 1)
            <div class="status-card success">
                <div class="card-body">
                    <h5>
                        <i class="bi bi-check-circle-fill"></i>
                        ¡Solicitud Aprobada!
                    </h5>
                    <p class="mb-0">Tu solicitud ha sido aceptada por el departamento y el encargado. Puedes continuar con el proceso de prácticas profesionales.</p>
                </div>
            </div>
        @elseif ($solicitud->Estado_Encargado === 'rechazado' || $solicitud->Estado_Departamento === 'rechazado')
            <div class="status-card danger">
                <div class="card-body">
                    <h5>
                        <i class="bi bi-x-circle-fill"></i>
                        Solicitud Rechazada
                    </h5>
                    <p class="mb-0">Revisa los comentarios a continuación y corrige las secciones marcadas antes de reenviarla.</p>
                </div>
            </div>
        @else
            <div class="status-card warning">
                <div class="card-body">
                    <h5>
                        <i class="bi bi-clock-fill"></i>
                        En Revisión
                    </h5>
                    <p class="mb-0">Tu solicitud está siendo revisada por el departamento y el encargado. Te notificaremos cuando haya una respuesta.</p>
                </div>
            </div>
        @endif

        <!-- Estados de Aprobación -->
        <div class="row mb-4">
            <!-- Estado Departamento -->
            <div class="col-md-6 mb-3">
                <div class="approval-card {{ $solicitud->Estado_Departamento === 'aprobado' ? 'approved' : ($solicitud->Estado_Departamento === 'rechazado' ? 'rejected' : 'pending') }}">
                    <div class="approval-header {{ $solicitud->Estado_Departamento === 'aprobado' ? 'approved' : ($solicitud->Estado_Departamento === 'rechazado' ? 'rejected' : 'pending') }}">
                        <div class="approval-title">
                            <i class="bi bi-building"></i>
                            Departamento DSSPP
                        </div>
                        @if($solicitud->Estado_Departamento === 'aprobado')
                            <span class="approval-status" style="background: #28a745; color: white;">
                                <i class="bi bi-check-circle-fill"></i> Aprobado
                            </span>
                        @elseif($solicitud->Estado_Departamento === 'rechazado')
                            <span class="approval-status" style="background: #dc3545; color: white;">
                                <i class="bi bi-x-circle-fill"></i> Rechazado
                            </span>
                        @else
                            <span class="approval-status" style="background: #ffc107; color: #000;">
                                <i class="bi bi-clock-fill"></i> Pendiente
                            </span>
                        @endif
                    </div>
                    @if(!empty($solicitud->Comentarios))
                        <div class="approval-body">
                            <div class="comment-label">
                                <i class="bi bi-chat-square-text-fill"></i>
                                Comentarios:
                            </div>
                            <div class="comment-text">{{ $solicitud->Comentarios }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estado Encargado -->
            <div class="col-md-6 mb-3">
                <div class="approval-card {{ $solicitud->Estado_Encargado === 'aprobado' ? 'approved' : ($solicitud->Estado_Encargado === 'rechazado' ? 'rejected' : 'pending') }}">
                    <div class="approval-header {{ $solicitud->Estado_Encargado === 'aprobado' ? 'approved' : ($solicitud->Estado_Encargado === 'rechazado' ? 'rejected' : 'pending') }}">
                        <div class="approval-title">
                            <i class="bi bi-person-badge"></i>
                            Encargado de PP
                        </div>
                        @if($solicitud->Estado_Encargado === 'aprobado')
                            <span class="approval-status" style="background: #28a745; color: white;">
                                <i class="bi bi-check-circle-fill"></i> Aprobado
                            </span>
                        @elseif($solicitud->Estado_Encargado === 'rechazado')
                            <span class="approval-status" style="background: #dc3545; color: white;">
                                <i class="bi bi-x-circle-fill"></i> Rechazado
                            </span>
                        @else
                            <span class="approval-status" style="background: #ffc107; color: #000;">
                                <i class="bi bi-clock-fill"></i> Pendiente
                            </span>
                        @endif
                    </div>
                    @if(isset($solicitud->autorizacion) && !empty($solicitud->autorizacion->Comentario_Encargado))
                        <div class="approval-body">
                            <div class="comment-label">
                                <i class="bi bi-chat-square-text-fill"></i>
                                Comentarios:
                            </div>
                            <div class="comment-text">{{ $solicitud->autorizacion->Comentario_Encargado }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SECCIÓN 1: DATOS GENERALES DEL SOLICITANTE -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-person-circle"></i>
                1. Datos Generales del Solicitante
            </div>
            <div class="section-body">
                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-calendar-event"></i>
                            Fecha de Solicitud
                        </div>
                        <div class="form-value">{{ \Carbon\Carbon::parse($solicitud->Fecha_Solicitud)->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="form-group full-width">
                        <div class="form-label-custom">
                            <i class="bi bi-person"></i>
                            Nombre Completo
                        </div>
                        <div class="form-value">
                            {{ $alumnoSession['nombres'] ?? $alumno->Nombre ?? '' }} 
                            {{ $alumnoSession['paterno'] ?? $alumno->ApellidoP_Alumno ?? '' }} 
                            {{ $alumnoSession['materno'] ?? $alumno->ApellidoM_Alumno ?? '' }}
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-key"></i>
                            Clave UASLP
                        </div>
                        <div class="form-value">{{ $alumnoSession['cve_uaslp'] ?? $alumno->Clave_Alumno ?? '' }}</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-journal-bookmark"></i>
                            Semestre
                        </div>
                        <div class="form-value">{{ $alumnoSession['semestre'] ?? $alumno->Semestre ?? 'N/A' }}</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-award"></i>
                            Número de Créditos
                        </div>
                        <div class="form-value">{{ $alumnoSession['creditos'] ?? $solicitud->Numero_Creditos ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <div class="form-label-custom">
                            <i class="bi bi-book"></i>
                            Carrera
                        </div>
                        <div class="form-value">{{ $alumnoSession['carrera'] ?? $alumno->Carrera ?? '' }}</div>
                    </div>
                </div>

                <div class="form-row">
                    <!-- CORREGIDO: Teléfono del alumno desde webservice -->
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-telephone"></i>
                            Teléfono
                        </div>
                        <div class="form-value">{{ $alumnoSession['telefono_celular'] ?? $solicitud->Telefono ?? $alumno->Telefono ?? 'N/A' }}</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-card-text"></i>
                            NSF
                        </div>
                        <div class="form-value">{{ $solicitud->NSF ?? 'N/A' }}</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-person-check"></i>
                            Estado
                        </div>
                        <div class="form-value">{{ $solicitud->Situacion_Alumno_Pasante == 1 ? 'Alumno' : 'Pasante' }}</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-shield-check"></i>
                            Tipo de Seguro
                        </div>
                        <div class="form-value">
                            @if($solicitud->Tipo_Seguro)
                                <span class="badge-check"><i class="bi bi-check"></i> IMSS</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No aplica</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-chat-dots"></i>
                            Inducción PP
                        </div>
                        <div class="form-value">
                            @if($solicitud->Induccion_Platicas)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-file-earmark-text"></i>
                            Estadística General
                        </div>
                        <div class="form-value">
                            @if($solicitud->Estadistica_General)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-file-earmark-check"></i>
                            Constancia Vigencia Derechos
                        </div>
                        <div class="form-value">
                            @if($solicitud->Constancia_Vig_Der)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Carta Pasante
                        </div>
                        <div class="form-value">
                            @if($solicitud->Carta_Pasante)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-mortarboard"></i>
                            Egresado Situación Especial
                        </div>
                        <div class="form-value">
                            @if($solicitud->Egresado_Sit_Esp)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-shield-plus"></i>
                            Extensión Seguro Facultativo
                        </div>
                        <div class="form-value">
                            @if($solicitud->Extension_Practicas)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: DATOS DE LAS PRÁCTICAS -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-briefcase"></i>
                2. Datos Generales de las Prácticas Profesionales
            </div>
            <div class="section-body">
                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-calendar-check"></i>
                            Fecha de Inicio
                        </div>
                        <div class="form-value">{{ \Carbon\Carbon::parse($solicitud->Fecha_Inicio)->format('d/m/Y') }}</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-calendar-x"></i>
                            Fecha de Término
                        </div>
                        <div class="form-value">{{ \Carbon\Carbon::parse($solicitud->Fecha_Termino)->format('d/m/Y') }}</div>
                    </div>
                </div>

                <!-- INFORMACIÓN DE EMPRESA/SECTOR -->
                @if($empresa)
                    <div class="subsection-title">
                        <i class="bi bi-building"></i>
                        Información de la Empresa
                    </div>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <div class="form-label-custom">
                                <i class="bi bi-building"></i>
                                Nombre de la Empresa
                            </div>
                            <div class="form-value">{{ $empresa->Nombre_Depn_Emp ?? 'N/A' }}</div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-card-heading"></i>
                                RFC
                            </div>
                            <div class="form-value">{{ $empresa->RFC_Empresa ?? 'N/A' }}</div>
                        </div>
                        <!-- CORREGIDO: Teléfono de la empresa -->
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-telephone"></i>
                                Teléfono
                            </div>
                            <div class="form-value">{{ $empresa->Telefono ?? 'N/A' }}</div></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-tag"></i>
                                Ramo
                            </div>
                            <div class="form-value">
                                @php
                                    $ramos = [
                                        '1' => 'Agricultura, ganadería y caza',
                                        '2' => 'Transporte y comunicaciones',
                                        '3' => 'Industria manufacturera',
                                        '4' => 'Restaurantes y hoteles',
                                        '5' => 'Servicios profesionales y técnicos especializados',
                                        '6' => 'Servicios de reparación y mantenimiento',
                                        '7' => 'Servicios educativos',
                                        '8' => 'Construcción',
                                        '9' => 'Otro'
                                    ];
                                @endphp
                                {{ $ramos[$empresa->Ramo] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-tags"></i>
                                Clasificación
                            </div>
                            <div class="form-value">{{ $empresa->Clasificacion == '1' ? 'Privado' : 'Público' }}</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <div class="form-label-custom">
                                <i class="bi bi-geo-alt"></i>
                                Dirección Completa
                            </div>
                            <div class="form-value">
                                {{ $empresa->Calle ?? '' }} #{{ $empresa->Numero ?? '' }}, 
                                {{ $empresa->Colonia ?? '' }}, 
                                {{ $empresa->Municipio ?? '' }}, 
                                {{ $empresa->Estado ?? '' }}, 
                                CP {{ $empresa->Cp ?? '' }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- SECTOR PRIVADO -->
                @if($sectorPrivado)
                    <div class="subsection-title">
                        <i class="bi bi-briefcase-fill"></i>
                        Información del Sector Privado
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-diagram-3"></i>
                                Área/Departamento
                            </div>
                            <div class="form-value">{{ $sectorPrivado->Area_Depto ?? 'N/A' }}</div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-people"></i>
                                Razón Social
                            </div>
                            <div class="form-value">{{ $sectorPrivado->Razon_Social ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-person-lines-fill"></i>
                                Número de Trabajadores
                            </div>
                            <div class="form-value">
                                @php
                                    $trabajadores = [
                                        '1' => 'Micro (1 - 30)',
                                        '2' => 'Pequeña (31 - 100)',
                                        '3' => 'Mediana (101 - 250)',
                                        '4' => 'Grande (más de 250)'
                                    ];
                                @endphp
                                {{ $trabajadores[$sectorPrivado->Num_Trabajadores] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-gear"></i>
                                Actividad o Giro
                            </div>
                            <div class="form-value">
                                @php
                                    $giros = [
                                        '1' => 'Extractiva',
                                        '2' => 'Manufacturera',
                                        '3' => 'Comercial',
                                        '4' => 'Comisionista',
                                        '5' => 'Servicio'
                                    ];
                                @endphp
                                {{ $giros[$sectorPrivado->Actividad_Giro] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-building-check"></i>
                                Empresa Outsourcing
                            </div>
                            <div class="form-value">
                                @if($sectorPrivado->Emp_Outsourcing)
                                    <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                                @else
                                    <span class="badge-x"><i class="bi bi-x"></i> No</span>
                                @endif
                            </div>
                        </div>
                        @if($sectorPrivado->Emp_Outsourcing)
                            <div class="form-group">
                                <div class="form-label-custom">
                                    <i class="bi bi-building-fill-gear"></i>
                                    Razón Social Outsourcing
                                </div>
                                <div class="form-value">{{ $sectorPrivado->Razon_Social_Outsourcing ?? 'N/A' }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- SECTOR PÚBLICO -->
                @if($sectorPublico)
                    <div class="subsection-title">
                        <i class="bi bi-bank"></i>
                        Información del Sector Público
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-diagram-3"></i>
                                Área/Departamento
                            </div>
                            <div class="form-value">{{ $sectorPublico->Area_Depto ?? 'N/A' }}</div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-globe"></i>
                                Ámbito
                            </div>
                            <div class="form-value">
                                @if($sectorPublico->Ambito == 1)
                                    Municipal
                                @elseif($sectorPublico->Ambito == 2)
                                    Estatal
                                @elseif($sectorPublico->Ambito == 3)
                                    Federal
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <!-- CORREGIDO: Teléfono del sector público -->
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-telephone"></i>
                                Teléfono
                            </div>
                            <div class="form-value">{{ $sectorPublico->Telefono ?? $solicitud->Telefono_Empresa ?? 'N/A' }}</div>
                    </div>
                @endif

                <!-- SECTOR UASLP -->
                @if($sectorUaslp)
                    <div class="subsection-title">
                        <i class="bi bi-mortarboard"></i>
                        Información del Sector UASLP
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-diagram-3"></i>
                                Área/Departamento
                            </div>
                            <div class="form-value">{{ $sectorUaslp->Area_Depto ?? 'N/A' }}</div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-bank"></i>
                                Tipo de Entidad
                            </div>
                            <div class="form-value">{{ $sectorUaslp->Tipo_Entidad == 1 ? 'Instituto' : 'Centro de Investigación' }}</div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-building"></i>
                                Entidad Académica
                            </div>
                            <div class="form-value">{{ $sectorUaslp->Id_Entidad_Academica ?? 'N/A' }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- SECCIÓN 3: ENCARGADO DE PRÁCTICAS / ASESOR EXTERNO -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-person-badge"></i>
                3. Encargado de Prácticas / Asesor Externo
            </div>
            <div class="section-body">
                @if($asesorExterno)
                    <div class="form-row">
                        <div class="form-group full-width">
                            <div class="form-label-custom">
                                <i class="bi bi-person"></i>
                                Nombre Completo del Asesor Externo
                            </div>
                            <div class="form-value">
                                {{ $asesorExterno->Nombre ?? '' }} 
                                {{ $asesorExterno->Apellido_Paterno ?? '' }} 
                                {{ $asesorExterno->Apellido_Materno ?? '' }}
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-diagram-3"></i>
                                Área
                            </div>
                            <div class="form-value">{{ $asesorExterno->Area ?? 'N/A' }}</div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-briefcase"></i>
                                Puesto
                            </div>
                            <div class="form-value">{{ $asesorExterno->Puesto ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-envelope"></i>
                                Correo Electrónico
                            </div>
                            <div class="form-value">{{ $asesorExterno->Correo ?? 'N/A' }}</div>
                        </div>
                        <!-- CORREGIDO: Teléfono del asesor externo -->
                        <div class="form-group">
                            <div class="form-label-custom">
                                <i class="bi bi-telephone"></i>
                                Teléfono
                            </div>
                            <div class="form-value">{{ $asesorExterno->Telefono ?? 'N/A' }}</div>
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-person-x"></i>
                        <p>No se ha asignado un asesor externo para esta solicitud</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- SECCIÓN 4: PROYECTO Y ACTIVIDADES -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-clipboard-check"></i>
                4. Proyecto y Actividades
            </div>
            <div class="section-body">
                <div class="form-row">
                    <div class="form-group full-width">
                        <div class="form-label-custom">
                            <i class="bi bi-file-text"></i>
                            Nombre del Proyecto
                        </div>
                        <div class="form-value {{ empty($solicitud->Nombre_Proyecto) ? 'empty' : '' }}">
                            {{ $solicitud->Nombre_Proyecto ?? 'No especificado' }}
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full-width">
                        <div class="form-label-custom">
                            <i class="bi bi-list-task"></i>
                            Actividades
                        </div>
                        <div class="form-value {{ empty($solicitud->Actividades) ? 'empty' : '' }}">
                            {{ $solicitud->Actividades ?? 'No especificadas' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 5: HORARIO -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-clock"></i>
                5. Horario
            </div>
            <div class="section-body">
                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-brightness-high"></i>
                            Turno
                        </div>
                        <div class="form-value">{{ $solicitud->Horario_Mat_Ves == 'M' ? 'Matutino' : 'Vespertino' }}</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-clock-history"></i>
                            Hora de Entrada
                        </div>
                        <div class="form-value">{{ $solicitud->Horario_Entrada ?? 'N/A' }}</div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-clock"></i>
                            Hora de Salida
                        </div>
                        <div class="form-value">{{ $solicitud->Horario_Salida ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <div class="form-label-custom">
                            <i class="bi bi-calendar-week"></i>
                            Días de Asistencia
                        </div>
                        <div class="form-value">
                            @php
                                $dias = $solicitud->Dias_Semana ?? '';
                                $diasArray = !empty($dias) ? explode(',', $dias) : [];
                                $diasNombres = [
                                    'L' => 'Lunes',
                                    'M' => 'Martes',
                                    'Mi' => 'Miércoles',
                                    'J' => 'Jueves',
                                    'V' => 'Viernes',
                                    'S' => 'Sábado',
                                    'D' => 'Domingo'
                                ];
                            @endphp
                            @if(!empty($diasArray))
                                @foreach($diasArray as $dia)
                                    <span class="badge-day">{{ $diasNombres[trim($dia)] ?? trim($dia) }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No especificado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 6: CRÉDITOS Y APOYO ECONÓMICO -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-cash-coin"></i>
                6. Créditos / Apoyo Económico
            </div>
            <div class="section-body">
                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-check-square"></i>
                            Validación de Créditos
                        </div>
                        <div class="form-value">
                            @if($solicitud->Validacion_Creditos)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-currency-dollar"></i>
                            Apoyo Económico
                        </div>
                        <div class="form-value">
                            @if($solicitud->Apoyo_Economico)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-arrow-repeat"></i>
                            Extensión de Prácticas
                        </div>
                        <div class="form-value">
                            @if($solicitud->Extension_Practicas)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-custom">
                            <i class="bi bi-receipt"></i>
                            Expedición de Recibos
                        </div>
                        <div class="form-value">
                            @if($solicitud->Expedicion_Recibos)
                                <span class="badge-check"><i class="bi bi-check"></i> Sí</span>
                            @else
                                <span class="badge-x"><i class="bi bi-x"></i> No</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <!-- CORREGIDO: DOCUMENTOS ADJUNTOS - Solo muestra PDFs que fueron subidos -->
        @if($pdfEstadistica || $pdfVigenciaDerechos || $pdfCartaPasante)
            <div class="section-card">
                <div class="section-header">
                    <i class="bi bi-file-earmark-pdf"></i>
                    Documentos Adjuntos
                </div>
                <div class="section-body">
                    <!-- Estadística General -->
                    @if($pdfEstadistica)
                        <div class="pdf-viewer-card mb-3">
                            <div class="pdf-header">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                Estadística General
                            </div>
                            <div class="pdf-body">
                                <iframe src="{{ asset('storage/' . $pdfEstadistica) }}" class="pdf-frame"></iframe>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ asset('storage/' . $pdfEstadistica) }}" target="_blank" class="btn-open-pdf">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                        Abrir en nueva pestaña
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Constancia Vigencia de Derechos -->
                    @if($pdfVigenciaDerechos)
                        <div class="pdf-viewer-card mb-3">
                            <div class="pdf-header">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                Constancia de Vigencia de Derechos
                            </div>
                            <div class="pdf-body">
                                <iframe src="{{ asset('storage/' . $pdfVigenciaDerechos) }}" class="pdf-frame"></iframe>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ asset('storage/' . $pdfVigenciaDerechos) }}" target="_blank" class="btn-open-pdf">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                        Abrir en nueva pestaña
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Carta Pasante -->
                    @if($pdfCartaPasante)
                        <div class="pdf-viewer-card mb-3">
                            <div class="pdf-header">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                Carta Pasante
                            </div>
                            <div class="pdf-body">
                                <iframe src="{{ asset('storage/' . $pdfCartaPasante) }}" class="pdf-frame"></iframe>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ asset('storage/' . $pdfCartaPasante) }}" target="_blank" class="btn-open-pdf">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                        Abrir en nueva pestaña
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Comentarios (si existen) -->
        @if (!empty($solicitud->Comentarios) || (isset($solicitud->autorizacion) && !empty($solicitud->autorizacion->Comentario_Encargado)))
            @if (!empty($solicitud->Comentarios))
                <div class="comment-card">
                    <div class="comment-header">
                        <i class="bi bi-chat-square-text-fill"></i>
                        Comentarios del Departamento
                    </div>
                    <div class="comment-body">
                        <p class="comment-text">{{ $solicitud->Comentarios }}</p>
                    </div>
                </div>
            @endif

            @if (isset($solicitud->autorizacion) && !empty($solicitud->autorizacion->Comentario_Encargado))
                <div class="comment-card">
                    <div class="comment-header">
                        <i class="bi bi-chat-square-text-fill"></i>
                        Comentarios del Encargado
                    </div>
                    <div class="comment-body">
                        <p class="comment-text">{{ $solicitud->autorizacion->Comentario_Encargado }}</p>
                    </div>
                </div>
            @endif
        @endif

        <!-- Botones de Acción -->
        <div class="action-buttons">
            <a href="{{ route('alumno.expediente.solicitudes') }}" class="btn-action btn-back">
                <i class="bi bi-arrow-left"></i>
                Volver al Historial
            </a>
            
            @if ($solicitud->Estado_Encargado === 'rechazado' || $solicitud->Estado_Departamento === 'rechazado')
                <a href="{{ route('alumno.expediente.solicitud.editar', $solicitud->Id_Solicitud_FPP01) }}" class="btn-action btn-edit">
                    <i class="bi bi-pencil-square"></i>
                    Corregir y Reenviar
                </a>
            @endif
        </div>
    </div>
</div>
@endsection