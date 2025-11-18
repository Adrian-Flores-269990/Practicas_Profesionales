@extends('layouts.alumno')

@section('title','Nuevo Reporte')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@section('content')
@include('partials.nav.registro')

<div class="container-fluid my-0 p-0">
  <!-- Header -->
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-file-earmark-text me-2"></i>
                FORMATO DE REPORTE MENSUAL DE ACTIVIDADES
            </h4>
        </div>
    </div>

  <div class="bg-white p-4 rounded shadow-sm w-100">
    <form id="form-reporte">
      {{-- Número de Reporte y Fechas --}}
      <div class="row mb-3 align-items-end">
        <div class="col-md-4">
          <label for="numero_reporte" class="form-label fw-bold">Número de Reporte:</label>
          <select id="numero_reporte" class="form-select">
            <option selected>Seleccione...</option>
            <option value="1">Reporte 1</option>
            <option value="2">Reporte 2</option>
            <option value="3">Reporte 3</option>
          </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Periodo:</label>
            <div class="d-flex gap-2">
                <input type="date" class="form-control" id="fechaInicio" name="fechaInicio">
                <span class="pt-2 align-self-center">a</span>
                <input type="date" class="form-control" id="fechaFin" name="fechaFin">
            </div>
        </div>

        <div class="col-md-4 text-end">
          <div class="mb-2">
            <strong>Clave:</strong> 194659
          </div>
          <small class="text-muted">Pendientes: miércoles 29 de octubre del 2025, 23:59</small>
        </div>
      </div>

      {{-- Resumen --}}
      <div class="mb-4">
        <label for="resumen" class="form-label fw-bold">Resumen de las actividades:</label>
        <textarea id="resumen" rows="4" class="form-control"></textarea>
      </div>

      {{-- Checkbox --}}
      <div class="mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="reporteFinal">
          <label class="form-check-label fw-semibold" for="reporteFinal">
            Reporte Final <small>(Seleccione en caso de que este sea su último reporte a subir)</small>
          </label>
        </div>
      </div>

      {{-- Área de envío de archivo --}}
      <div class="mb-4 border rounded p-3 bg-light">
        <h6 class="fw-bold mb-3">
          <i class="bi bi-upload"></i> Añadir envío
        </h6>

        <div class="border rounded border-dashed p-4 text-center bg-white position-relative" style="min-height: 180px;" id="zonaSubida">
          <div id="archivoInstrucciones">
            <div class="mb-2">
              <i class="bi bi-cloud-upload display-6 text-muted"></i>
            </div>
            <p class="text-muted">Arrastre y suelte los archivos aquí para subirlos</p>
            <p class="small text-danger">Archivos de tamaño igual o menor a 20MB, únicamente archivos en PDF</p>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="botonSubir">Seleccionar Archivos</button>
          </div>

          <input type="file" class="form-control d-none" id="archivoUpload" accept=".pdf">

          <div id="archivoPreview" class="mt-3 d-none">
            <div class="card border-primary shadow-sm">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1" id="archivoNombre"></h6>
                  <p class="mb-0 text-muted small" id="archivoTamaño"></p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" id="btnEliminarArchivo">
                  <i class="bi bi-trash"></i> Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Botones --}}
      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary">Guardar cambios</button>
        <button type="button" class="btn btn-danger" onclick="resetFormulario()">Cancelar</button>
        <button type="submit" class="btn btn-success">Enviar</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  const inputArchivo = document.getElementById('archivoUpload');
  const botonSubir = document.getElementById('botonSubir');
  const instrucciones = document.getElementById('archivoInstrucciones');
  const preview = document.getElementById('archivoPreview');
  const nombreArchivo = document.getElementById('archivoNombre');
  const tamañoArchivo = document.getElementById('archivoTamaño');
  const btnEliminar = document.getElementById('btnEliminarArchivo');
  const zonaSubida = document.getElementById('zonaSubida');

  // Mostrar input al hacer clic en el botón
  botonSubir.addEventListener('click', () => {
    inputArchivo.click();
  });

  // Cuando se selecciona un archivo
  inputArchivo.addEventListener('change', () => {
    if (inputArchivo.files.length > 0) {
      const file = inputArchivo.files[0];

      // Oculta instrucciones y muestra preview
      instrucciones.classList.add('d-none');
      preview.classList.remove('d-none');
      nombreArchivo.textContent = file.name;
      tamañoArchivo.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    }
  });

  // Eliminar archivo
  btnEliminar.addEventListener('click', () => {
    inputArchivo.value = "";
    preview.classList.add('d-none');
    instrucciones.classList.remove('d-none');
  });

  // Cancelar: limpia todo el formulario
  function resetFormulario() {
  const form = document.getElementById('form-reporte');
  form.reset();

  // Reset manual del input file
  inputArchivo.value = "";

  // Ocultar preview y mostrar instrucciones de nuevo
  preview.classList.add('d-none');
  instrucciones.classList.remove('d-none');
}

</script>
@endpush
@endsection
