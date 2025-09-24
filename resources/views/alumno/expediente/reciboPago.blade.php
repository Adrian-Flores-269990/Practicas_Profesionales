@extends('layouts.alumno')

@section('title','Recibo de Pago')

@section('content')

<div class="container-fluid my-0 p-0">

  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    RECIBO DE PAGO
  </h4>

  <div class="bg-white p-4 rounded shadow-sm w-100">
    <form>
        {{-- Área de descarga de archivo --}}
      <div class="mb-4 border rounded p-3 bg-light">
        <h6 class="fw-bold mb-3">
          <i class="bi bi-download"></i> Descargar el recibo de pago más reciente
        </h6>

        <div class="border rounded border-dashed p-4 text-center bg-white position-relative" style="min-height: 180px;">
          <div id="archivoDescarga">
            <div class="mb-2">
              <i class="bi bi-cloud-download display-6 text-muted"></i>
            </div>
            <p class="text-muted">Haz clic en el botón para descargar tu documento</p>
            <p class="small text-primary">Formato: PDF</p>
            <a href="{{ asset('storage/documentos/carta_desglose.pdf') }}"
               class="btn btn-outline-primary btn-sm"
               download>
               Descargar Documento
            </a>
          </div>
        </div>
      </div>

      {{-- Botones --}}
      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary">Guardar cambios</button>
        <button type="button" class="btn btn-danger" onclick="resetFormulario()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  // Cancelar: limpia todo el formulario
  function resetFormulario() {
    const form = document.querySelector('form');
    form.reset();
  }
</script>
@endpush

@endsection
