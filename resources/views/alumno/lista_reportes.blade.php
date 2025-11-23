@extends('layouts.alumno')

@section('title','Mis Reportes')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">

@section('content')
@include('partials.nav.registro')

<div class="container-fluid my-0 p-0">
    <!-- Header -->
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-folder2-open me-2"></i>
                MIS REPORTES MENSUALES
            </h4>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow-sm w-100">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">Reportes Enviados: <span class="badge bg-primary">{{ $reportes->count() }}</span></h5>
                <small class="text-muted">Clave: {{ $alumno['cve_uaslp'] ?? 'N/A' }}</small>
            </div>
            <a href="{{ route('alumno.reporte') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nuevo Reporte
            </a>
        </div>

        @if($reportes->isEmpty())
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                No has enviado ningún reporte todavía.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No. Reporte</th>
                            <th>Periodo</th>
                            <th>Fecha Envío</th>
                            <th>Estado</th>
                            <th>Calificación</th>
                            <th>Archivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportes as $reporte)
                            <tr>
                                <td>
                                    <strong>Reporte {{ $reporte->Numero_Reporte }}</strong>
                                    @if($reporte->Reporte_Final)
                                        <span class="badge bg-success ms-2">Final</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        {{ \Carbon\Carbon::parse($reporte->Periodo_Ini)->format('d/m/Y') }}
                                        - 
                                        {{ \Carbon\Carbon::parse($reporte->Periodo_Fin)->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($reporte->Periodo_Fin)->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if($reporte->Calificacion !== null)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Calificado
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($reporte->Calificacion !== null)
                                        <span class="badge bg-primary fs-6">{{ $reporte->Calificacion }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($reporte->Archivo_Agregado)
                                        <i class="bi bi-file-pdf text-danger"></i>
                                        <small>PDF</small>
                                    @else
                                        <span class="text-muted">Sin archivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalDetalle{{ $reporte->Id_Reporte }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($reporte->Archivo_Agregado && $reporte->Nombre_Archivo)
                                            <a href="{{ route('alumno.reportes.descargar', $reporte->Id_Reporte) }}" 
                                               class="btn btn-outline-danger" 
                                               title="Descargar PDF">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Detalle -->
                            <div class="modal fade" id="modalDetalle{{ $reporte->Id_Reporte }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="bi bi-file-text"></i>
                                                Detalle del Reporte {{ $reporte->Numero_Reporte }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Periodo:</strong><br>
                                                    Del {{ \Carbon\Carbon::parse($reporte->Periodo_Ini)->format('d/m/Y') }}
                                                    al {{ \Carbon\Carbon::parse($reporte->Periodo_Fin)->format('d/m/Y') }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Tipo:</strong><br>
                                                    @if($reporte->Reporte_Final)
                                                        <span class="badge bg-success">Reporte Final</span>
                                                    @else
                                                        <span class="badge bg-info">Reporte Parcial</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <strong>Resumen de Actividades:</strong>
                                                <div class="border rounded p-3 bg-light mt-2" style="white-space: pre-wrap;">{{ $reporte->Resumen_Actividad }}</div>
                                            </div>

                                            @if($reporte->Calificacion !== null)
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Calificación:</strong><br>
                                                        <span class="badge bg-primary fs-4">{{ $reporte->Calificacion }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($reporte->Observaciones)
                                                <div class="mb-3">
                                                    <strong>Observaciones del Encargado:</strong>
                                                    <div class="alert alert-warning mt-2">
                                                        <i class="bi bi-chat-left-text"></i>
                                                        {{ $reporte->Observaciones }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($reporte->Archivo_Agregado && $reporte->Nombre_Archivo)
                                                <div class="mb-3">
                                                    <strong>Archivo:</strong><br>
                                                    <a href="{{ route('alumno.reportes.descargar', $reporte->Id_Reporte) }}" 
                                                       class="btn btn-outline-danger btn-sm mt-2">
                                                        <i class="bi bi-file-pdf"></i> {{ $reporte->Nombre_Archivo }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
