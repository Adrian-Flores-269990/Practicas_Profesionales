@extends('layouts.alumno')

@section('title', 'Confirmación de datos FPP02')

@push('styles')
<style>


  .info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
  }

  .info-row:last-child {
    border-bottom: none;
  }

  .info-label {
    font-weight: 500;
    color: #555;
  }

  .info-value {
    color: #222;
  }

  .btn-row {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
  }

  .btn-aceptar {
    background: #004795;
    color: white;
    padding: 0.8rem 2rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: 0.2s;
  }

  .btn-aceptar:hover {
    background: #003b70;
  }

  .seccion-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
  }

  .seccion-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  }

  .seccion-header {
    background: #c3cfe2;
    padding: 1.25rem 1.5rem;
    font-weight: 700;
    font-size: 1.05rem;
    color: #2d3748;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .seccion-header i {
    font-size: 1.2rem;
  }

  .seccion-body {
    padding: 1.5rem;
  }

  .dato-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .dato-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .dato-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .dato-valor {
    color: #2d3748;
    font-size: 1rem;
    font-weight: 500;
  }
</style>
@endpush

@section('content')
@include('partials.nav.registro')
@php $alumno = $solicitud->alumno; @endphp
<div class="container mt-4">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    REVISIÓN DE SOLICITUD DE PRÁCTICAS PROFESIONALES
  </h4>
  {{-- Datos del Alumno --}}
  <div class="seccion-card">
    <div class="seccion-header" onclick="toggleSection(this)">
        <i class="bi bi-person-badge"></i>
            DATOS DE ALUMNO
        <i class="bi bi-chevron-down ms-auto status-icon"></i>
    </div>
    <div class="seccion-body">
        <div class="info-row">
            <span class="info-label">Nombre:</span>
            <span class="info-value">
                {{ $alumno->Nombre ?? 'No disponible' }}
                {{ $alumno->ApellidoP_Alumno ?? '' }}
                {{ $alumno->ApellidoM_Alumno ?? '' }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Clave UASLP:</span>
            <span class="info-value">{{ $alumno['Clave_Alumno'] ?? 'No disponible' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Carrera:</span>
            <span class="info-value">{{ $alumno->Carrera ?? 'No disponible' }}</span>
        </div>
    </div>
  </div>

  {{-- Datos de la Empresa --}}
  <div class="seccion-card">
    <div class="seccion-header" onclick="toggleSection(this)">
        <i class="bi bi-building"></i>
            DATOS DE LA EMPRESA
        <i class="bi bi-chevron-down ms-auto status-icon"></i>
    </div>
    <div class="seccion-body">
        <div class="info-row">
            <span class="info-label">Nombre de la empresa:</span>
            <span class="info-value">{{ $empresa->Nombre_Depn_Emp ?? 'No disponible' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">RFC:</span>
            <span class="info-value">{{ $alumno->RFC ?? 'No disponible' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dirección:</span>
            <span class="info-value">{{ $alumno->Direccion ?? 'No disponible' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Teléfono:</span>
            <span class="info-value">{{ $alumno->Telefono ?? 'No disponible' }}</span>
        </div>
    </div>
  </div>

  {{-- Datos del Proyecto --}}
  <div class="seccion-card">
    <div class="seccion-header" onclick="toggleSection(this)">
        <i class="bi bi-clipboard-check"></i>
            DATOS DEL PROYECTO
        <i class="bi bi-chevron-down ms-auto status-icon"></i>
    </div>
    <div class="seccion-body">
        <div class="info-row">
            <span class="info-label">Nombre del Proyecto:</span>
            <span class="info-value">{{ $solicitud->Nombre_Proyecto ?? 'No disponible' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Área de aplicación:</span>
            <span class="info-value">{{ $solicitud->Area_Aplicacion ?? 'No disponible' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Horario:</span>
            <span class="info-value">{{ $solicitud->Horario ?? 'No disponible' }}</span>
        </div>
    </div>
  </div>

  {{-- Botones de acción --}}
    <div class="btn-row">
        <form action="{{ route('alumno.confirma') }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="btn-aceptar">Imprimir FPP02</button>
        </form>
    </div>

</div>
@endsection
