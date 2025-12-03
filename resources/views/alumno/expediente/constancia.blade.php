@extends('layouts.alumno')

@section('title','Constancia de Validación de Prácticas Profesionales')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">

@section('content')

<div class="container-fluid my-0 p-0">

  <!-- Header -->
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-file-earmark-text me-2"></i>
                CONSTANCIA DE FINALIZACIÓN DE PRÁCTICAS PROFESIONALES
            </h4>
        </div>
    </div>

  <div class="bg-white p-4 rounded shadow-sm w-100">
      <div class="mb-4 border rounded p-3 bg-light">
        <h6 class="fw-bold mb-3">
          <i class="bi bi-download"></i> Descargar la constancia de finalizacion de prácticas profesionales
        </h6>

        <div class="border rounded border-dashed p-4 text-center bg-white position-relative" style="min-height: 180px;">
          <div id="archivoDescarga">
            <div class="mb-2">
              <i class="bi bi-cloud-download display-6 text-muted"></i>
            </div>
            @if(isset($pdfPath) && $pdfPath)
              <p class="text-muted">Haz clic en el botón para descargar tu recibo</p>
              <p class="small text-primary">Formato: PDF</p>
            <a href="{{ asset($pdfPath) }}"
              class="btn btn-outline-primary btn-sm">
              Descargar Constancia PDF
            </a>
          @else
            <p class="text-muted mb-0">No hay recibo disponible.</p>
          @endif
        </div>
      </div>
    </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
</script>
@endpush

@endsection
