@extends('layouts.alumno')

@section('title','Recibo de Pago')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">

@section('content')

<div class="container-fluid my-0 p-0">

  <!-- Header -->
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-file-earmark-text me-2"></i>
                RECIBO DE PAGO
            </h4>
        </div>
    </div>

  <div class="bg-white p-4 rounded shadow-sm w-100">
  {{-- Estado del último registro de pago --}}
      @if(isset($ultimoPago) && $ultimoPago)
        <div class="alert alert-success mb-3">
          <strong>Último registro:</strong><br>
          Folio: {{ $ultimoPago->Id_Solicitud_Pago }}<br>
          Periodo: {{ $ultimoPago->Fecha_Inicio_Pago }} al {{ $ultimoPago->Fecha_Termino_Pago }}<br>
          Salario: ${{ number_format($ultimoPago->Salario,2) }}<br>
          Fecha Solicitud: {{ $ultimoPago->Fecha_Solicitud }}<br>
          Fecha Entrega: {{ $ultimoPago->Fecha_Entrega }}
        </div>
      @else
        <div class="alert alert-warning mb-3">
          Aún no existe una solicitud de pago registrada. Genera primero tu <strong>Solicitud de Recibo para Ayuda Económica</strong>.
        </div>
      @endif

      <div class="mb-4 border rounded p-3 bg-light">
        <h6 class="fw-bold mb-3">
          <i class="bi bi-download"></i> Descargar el recibo de pago más reciente
        </h6>

        <div class="border rounded border-dashed p-4 text-center bg-white position-relative" style="min-height: 180px;">
          <div id="archivoDescarga">
            <div class="mb-2">
              <i class="bi bi-cloud-download display-6 text-muted"></i>
            </div>
            @if(isset($ultimoPago) && $ultimoPago)
              <p class="text-muted">Haz clic en el botón para descargar tu recibo</p>
              <p class="small text-primary">Formato: PDF</p>
            <a href="{{ route('alumno.expediente.reciboPago.descargar') }}"
               class="btn btn-outline-primary btn-sm">
               Descargar Recibo PDF
            </a>
          @else
            <p class="text-muted mb-0">No hay recibo disponible.</p>
          @endif
        </div>
      </div>
    </div>

      <div class="d-flex justify-content-end gap-2">
      <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
      <button type="button" class="btn btn-danger" onclick="resetFormulario()">Cancelar</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Cancelar: limpia todo el formulario
  function resetFormulario() {
    // No hay formulario editable aquí; función decorativa
    location.reload();
  }
</script>
@endpush

@endsection
