@extends('layouts.alumno')

@section('title','Carta de Desglose de Percepciones')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">

@section('content')

<div class="container-fluid my-0 p-0">
    <!-- Header -->
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-file-earmark-text me-2"></i>
                CARTA DE DESGLOSE DE PERCEPCIONES
            </h4>
        </div>
    </div>

  <div class="bg-white p-4 rounded shadow-sm w-100">
    
    {{-- Mensajes de éxito --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Mensajes de error --}}
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Mostrar PDF subido si existe --}}
    @php
      $alumno = session('alumno');
      $claveAlumno = $alumno['cve_uaslp'] ?? null;
    @endphp

    @if($pdfPath)
      <div class="mb-4">
        <h6 class="fw-bold">Documento subido:</h6>
        <iframe src="{{ asset($pdfPath) }}" width="100%" height="500px" style="border:1px solid #4583B7;"></iframe>
        <div class="d-flex gap-2 mt-2">
          <a href="{{ asset($pdfPath) }}" target="_blank" class="btn btn-outline-primary">Abrir PDF en nueva pestaña</a>
          <form method="POST" action="{{ route('desglosePercepciones.eliminar', ['claveAlumno' => $alumno['cve_uaslp'], 'tipo' => 'Carta_Desglose_Percepciones']) }}" style="display:inline;">
            @csrf
            <input type="hidden" name="archivo" value="{{ $pdfPath }}">
            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar el documento actual?')">
              <i class="bi bi-trash"></i> Eliminar PDF
            </button>
          </form>
        </div>
      </div>
    @else
      <form method="POST" action="{{ route('desglosePercepciones.upload') }}" enctype="multipart/form-data" id="form-reporte">
        @csrf
        {{-- Área de envío de archivo --}}
      <div class="mb-4 border rounded p-3 bg-light">
          <h6 class="fw-bold mb-3">
            <i class="bi bi-upload"></i> Subir documento emitido por la empresa
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
            
            <input type="file" class="form-control d-none" id="archivoUpload" accept=".pdf" name="archivo" required>

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
    @endif
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
