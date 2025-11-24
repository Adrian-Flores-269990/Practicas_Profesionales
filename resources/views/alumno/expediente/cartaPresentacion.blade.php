@extends('layouts.alumno')

@section('title','Carta de Presentación')

@push('styles')
<style>
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
    background: #17a2b8;
    color: white;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    font-size: 1.2rem;
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

  .btn-descargar {
    background: #0d6efd;
    color: white;
    border: none;
    padding: 0.85rem 2.5rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    display: inline-block;
    text-decoration: none;
  }

  .btn-descargar:hover {
    background: #0b5ed7;
    color: white;
    transform: translateY(-2px);
  }

  .btn-eliminar {
    background: #f01a1aff;
    color: white;
    border: none;
    padding: 0.85rem 2.5rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    display: inline-block;
    text-decoration: none;
  }

  .btn-eliminar:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245,101,101,0.4);
  }

  .btn-regresar {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.85rem 2.5rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    display: inline-block;
    text-decoration: none;
  }

  .btn-regresar:hover {
    background: #5a6268;
    color: white;
    transform: translateX(-4px);
  }

  .btn-enviar {
    background: #1f8950ff;
    color: white;
    border: none;
    padding: 0.85rem 2.5rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    display: inline-block;
    text-decoration: none;
  }

  .btn-enviar:hover {
    background: #198754;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(72,187,120,0.4);
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

  .upload-box {
    border: 2px dashed #6c757d;
    padding: 2rem;
    text-align: center;
    border-radius: 10px;
    background: #f8f9fa;
    transition: all 0.3s ease;
  }

  .upload-box:hover {
    border-color: #495057;
    background: #e9ecef;
  }

  .upload-box p {
    color: #6c757d;
    margin-bottom: 0.75rem;
    font-weight: 500;
  }

  .upload-box input[type="file"] {
    max-width: 500px;
    margin: 0 auto;
  }
</style>
@endpush

@section('content')

@php
    // Llega desde el controlador
    $alumno = session('alumno');
    $clave = $alumno['cve_uaslp'] ?? null;
@endphp

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    CARTA DE PRESENTACIÓN
  </h4>

  <div class="container py-4">

    {{-- =======================================
        SI YA EXISTE CARTA FIRMADA → SOLO PREVIEW
       ======================================= --}}
    @if($pdfPathFirmada ?? false)
      <div class="seccion-card">
        <div class="seccion-header">
          <i class="bi bi-file-earmark-check-fill"></i>
          Carta de Presentación Firmada
        </div>
        <div class="seccion-body">
          <iframe src="{{ $pdfPathFirmada }}" width="100%" height="500px" style="border:1px solid #4583B7; border-radius:8px;"></iframe>
          <div class="d-flex gap-2 mt-2">
            <a href="{{ $pdfPathFirmada }}" target="_blank" class="btn-open-pdf">
              <i class="bi bi-box-arrow-up-right"></i>
              Abrir en nueva pestaña
            </a>
          </div>

          <div class="mt-4 text-center d-flex gap-3 justify-content-center">
            <a href="{{ route('alumno.estado') }}" class="btn-regresar">
              <i class="bi bi-arrow-left me-2"></i>
              Regresar al Estado
            </a>

            <form action="{{ route('cartaPresentacion.eliminar', [
                    'claveAlumno' => $clave,
                    'tipo' => 'Carta_Presentacion_Firmada'
                ]) }}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="btn-eliminar" onclick="return confirm('¿Eliminar documento?')">
                <i class="bi bi-trash-fill me-2"></i>
                Eliminar Documento
              </button>
            </form>
          </div>
        </div>
      </div>

    @else
      {{-- ============================
          1. DESCARGAR CARTA GENERADA
         ============================ --}}
      <div class="seccion-card">
        <div class="seccion-header">
          <i class="bi bi-download"></i>
          Descargar Carta de Presentación
        </div>
        <div class="seccion-body">
          @if($pdfPath)
            <p class="text-muted mb-4">
              La carta ya fue generada por DSSPP y revisada por el Encargado. Puedes descargarla para llevarla a la empresa.
            </p>

            <div class="text-center">
              <a href="{{ asset($pdfPath) }}" class="btn-descargar" download>
                <i class="bi bi-download me-2"></i>
                Descargar Carta Presentación PDF
              </a>
            </div>
          @else
            <div class="alert alert-warning mb-0">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Aún no existe una carta de presentación generada por DSSPP.
            </div>
          @endif
        </div>
      </div>

      {{-- ============================
          2. SUBIR CARTA FIRMADA
         ============================ --}}
      <div class="seccion-card">
        <div class="seccion-header">
          <i class="bi bi-upload"></i>
          Subir Carta Firmada por la Empresa
        </div>
        <div class="seccion-body">
          @if(!$pdfPath)
            <div class="alert alert-secondary mb-0">
              <i class="bi bi-info-circle me-2"></i>
              Debes esperar a que DSSPP genere tu carta para poder subir la firmada.
            </div>
          @else
            <form action="{{ route('cartaPresentacion.upload') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="claveAlumno" value="{{ $clave }}">

              <div class="upload-box mb-3">
                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #6c757d; display: block; margin-bottom: 1rem;"></i>
                <p>Arrastra o selecciona la carta firmada</p>
                <p style="font-size: 0.85rem; color: #999;">Solo PDF, máximo 20 MB</p>
                <input type="file" name="archivo" accept="application/pdf" class="form-control mt-2" required>
              </div>

              <div class="text-center d-flex gap-3 justify-content-center">
                <button type="reset" class="btn-eliminar">
                  <i class="bi bi-x-circle me-2"></i>
                  Cancelar
                </button>
                <button type="submit" class="btn-enviar">
                  <i class="bi bi-check-circle me-2"></i>
                  Enviar
                </button>
              </div>
            </form>
          @endif
        </div>
      </div>
    @endif

  </div>
</div>

@endsection