@extends('layouts.dsspp')
@section('title', 'Vista previa de Carta')

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

  .btn-aceptar {
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

  .btn-aceptar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(72,187,120,0.4);
    color: white;
  }

  .btn-rechazar {
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

  .btn-rechazar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245,101,101,0.4);
    color: white;
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
</style>
@endpush

@section('content')
<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    VISTA PREVIA DE CARTA DE PRESENTACIÓN
  </h4>

  <div class="container py-4">
    {{-- Alerta --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    {{-- PREVIEW DEL PDF --}}
    <div class="seccion-card">
      <div class="seccion-header">
        <i class="bi bi-file-earmark-pdf-fill"></i>
        Carta de Presentación
      </div>
      <div class="seccion-body">
        @if($pdfPath)
          <iframe src="{{ $pdfPath }}" width="100%" height="500px" style="border:1px solid #4583B7; border-radius:8px;"></iframe>
          <div class="d-flex gap-2 mt-2">
            <a href="{{ $pdfPath }}" target="_blank" class="btn-open-pdf">
              <i class="bi bi-box-arrow-up-right"></i>
              Abrir en nueva pestaña
            </a>
          </div>
        @else
          <div class="alert alert-warning mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No se encontró el archivo PDF.
          </div>
        @endif
      </div>
    </div>

    {{-- BOTONES FINALES --}}
    <div class="mt-4 text-center d-flex gap-3 justify-content-center">

      {{-- ACEPTAR --}}
      <form action="{{ route('dsspp.carta.aprobar', request()->clave) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn-aceptar">
          <i class="bi bi-check-circle-fill me-2"></i>
          Aceptar
        </button>
      </form>


      {{-- RECHAZAR --}}
      <form action="{{ route('dsspp.carta.rechazar', request()->clave) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn-rechazar">
          <i class="bi bi-x-circle-fill me-2"></i>
          Rechazar
        </button>
      </form>

      {{-- REGRESAR --}}      
      <a href="{{ route('dsspp.carta') }}" class="btn-regresar">
        <i class="bi bi-arrow-left me-2"></i>
        Volver al listado
      </a>



    </div>
  </div>
</div>
@endsection