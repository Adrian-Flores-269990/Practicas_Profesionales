@extends('layouts.secretaria')

@section('title','Consultar Constancias')

@push('styles')
<style>
  .stats-card {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    text-align: center;
  }
  
  .stat-number {
    font-size: 2rem;
    font-weight: 700;
  }
  
  .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-top: 0.5rem;
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
  
  .custom-table thead {
    background: linear-gradient(135deg, #d4edda 0%, #a8d5ba 100%);
  }
  
  .custom-table thead th {
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: #155724;
    border-bottom: 2px solid #28a745;
    padding: 1rem;
  }
  
  .custom-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
  }
  
  .custom-table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.005);
  }
  
  .custom-table tbody td {
    padding: 1rem;
    vertical-align: middle;
  }
  
  .folio-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
  }
  
  .fecha-badge {
    background-color: #e7f3ff;
    color: #0c5460;
    padding: 0.35rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
  }
  
  .btn-action {
    padding: 0.4rem 0.9rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease;
  }
  
  .btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
  }
  
  .btn-download {
    background: linear-gradient(135deg, #2196f3 0%, #21cbf3 100%);
    color: white;
    border: none;
  }
  
  .btn-view {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border: none;
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
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40,167,69,.25);
  }
  
  .alert-success-custom {
    background: #d4edda;
    border-left: 4px solid #28a745;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
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
  
  .export-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
  }
  
  .export-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102,126,234,0.4);
    color: white;
  }
  
  .highlight-row {
    background-color: #fff3cd !important;
    animation: highlight 1s ease;
  }
  
  @keyframes highlight {
    0% { background-color: #ffc107; }
    100% { background-color: #fff3cd; }
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    CONSULTAR CONSTANCIAS GENERADAS
  </h4>
  
  <div class="p-4">
    

    {{-- Información --}}
    <div class="alert-success-custom">
      <i class="bi bi-check-circle-fill me-2"></i>
      <strong>Registro de Constancias:</strong> Aquí puedes consultar todas las constancias generadas, descargarlas o visualizar los detalles de cada una.
    </div>

    {{-- Filtros y búsqueda --}}
    <div class="filter-card">
      <div class="row align-items-center g-3">
        <div class="col-md-5">
          <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input 
              type="text" 
              class="form-control search-input" 
              placeholder="🔍 Buscar por clave, nombre o folio..."
              id="searchInput"
            >
          </div>
        </div>
        <div class="col-md-3">
          <select class="form-select" id="filterMes">
            <option value="">Todos los meses</option>
            <option value="01">Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
        </div>
        <div class="col-md-2">
          <select class="form-select" id="filterAnio">
            <option value="">Todos los años</option>
            <option value="2025">2025</option>
            <option value="2024">2024</option>
            <option value="2023">2023</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn export-btn w-100" onclick="exportarConstancias()">
            <i class="bi bi-file-earmark-excel me-2"></i>Exportar
          </button>
        </div>
      </div>
    </div>

    {{-- Tabla de constancias --}}
    @if(count($constancias) > 0)
      <div class="table-container">
        <table class="table custom-table mb-0">
          <thead>
            <tr>
              <th>Folio</th>
              <th>Clave</th>
              <th>Nombre del Alumno</th>
              <th>Carrera</th>
              <th>Fecha Generación</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>

            @foreach($constancias as $constancia)
              <tr data-folio="{{ $constancia['folio'] }}" data-fecha="{{ $constancia['fecha_generacion'] }}">
                <td>
                  <span class="folio-badge">{{ $constancia['folio'] }}</span>
                </td>
                <td>
                  <strong class="text-primary">{{ $constancia['clave'] }}</strong>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                         style="width: 35px; height: 35px; font-weight: 600; font-size: 0.9rem;">
                      {{ strtoupper(substr($constancia['nombre'], 0, 1)) }}
                    </div>
                    <div>
                      <div class="fw-semibold">{{ $constancia['nombre'] }}</div>
                      <small class="text-muted">{{ $constancia['correo'] ?? 'N/A' }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <i class="bi bi-mortarboard-fill text-success me-1"></i>
                  {{ $constancia['carrera'] }}
                </td>
                <td>
                  <span class="fecha-badge">
                    <i class="bi bi-calendar-check me-1"></i>
                    {{ \Carbon\Carbon::parse($constancia['fecha_generacion'])->format('d/m/Y') }}
                  </span>
                </td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <button 
                      class="btn btn-action btn-view btn-sm" 
                      onclick="verConstancia('{{ $constancia['folio'] }}')"
                      title="Ver Constancia"
                    >
                      <i class="bi bi-eye-fill"></i>
                    </button>
                    <button 
                      class="btn btn-action btn-download btn-sm" 
                      onclick="descargarConstancia('{{ $constancia['folio'] }}', '{{ $constancia['nombre'] }}')"
                      title="Descargar PDF"
                    >
                      <i class="bi bi-download"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        </div>
    @endif
@endsection

