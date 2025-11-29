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
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div id="mensajeAlerta" class="alert d-none" role="alert"></div>

    <form id="form-reporte" enctype="multipart/form-data">
      @csrf
      {{-- Número de Reporte y Fechas --}}
      <div class="row mb-3 align-items-end">
        <div class="col-md-4">
          <label for="numero_reporte" class="form-label fw-bold">Número de Reporte: <span class="text-danger">*</span></label>
          <select id="numero_reporte" name="numero_reporte" class="form-select" required>
            <option value="">Seleccione...</option>

            @if(isset($allowedReportes) && $allowedReportes && $allowedReportes->count() > 0)
                @foreach($allowedReportes as $num)
                    <option value="{{ $num }}">Reporte {{ $num }}</option>
                @endforeach
            @else
                {{-- Fallback: 1..12 --}}
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">Reporte {{ $i }}</option>
                @endfor
            @endif

            {{-- Opción siempre disponible para Reporte Final --}}
            <option value="100">Reporte Final</option>
        </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Periodo: <span class="text-danger">*</span></label>
            <div class="d-flex gap-2">
                <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio" required>
                <span class="pt-2 align-self-center">a</span>
                <input type="date" class="form-control" id="fechaFin" name="fecha_fin" required>
            </div>
        </div>

        <div class="col-md-4 text-end">
          <div class="mb-2">
            <strong>Clave:</strong> {{ $alumno['cve_uaslp'] ?? 'N/A' }}
          </div>
          <small class="text-muted">Reportes enviados: {{ $reportesExistentes->count() }}</small>
        </div>
      </div>

      {{-- Resumen --}}
      <div class="mb-4">
        <label for="resumen" class="form-label fw-bold">Resumen de las actividades: <span class="text-danger">*</span></label>
        <textarea id="resumen" name="resumen" rows="4" class="form-control" required
                  placeholder="Describa las actividades realizadas durante el periodo..."></textarea>
        <small class="text-muted">Máximo 5000 caracteres</small>
      </div>

      {{-- Checkbox --}}
      <div class="mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="reporteFinal" name="reporte_final" value="1">
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

          <input type="file" class="form-control d-none" id="archivoUpload" name="archivo_pdf" accept=".pdf">

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
        <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('alumno.reportes.lista') }}'">
          <i class="bi bi-list"></i> Ver mis reportes
        </button>
        <button type="button" class="btn btn-danger" onclick="resetFormulario()">Cancelar</button>
        <button type="submit" class="btn btn-success" id="btnEnviar">
          <i class="bi bi-send"></i> Enviar
        </button>
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
  const mensajeAlerta = document.getElementById('mensajeAlerta');
  const btnEnviar = document.getElementById('btnEnviar');

  // Mostrar input al hacer clic en el botón
  botonSubir.addEventListener('click', () => {
    inputArchivo.click();
  });

  // Cuando se selecciona un archivo
  inputArchivo.addEventListener('change', () => {
    if (inputArchivo.files.length > 0) {
      const file = inputArchivo.files[0];

      // Validar tamaño (20MB máximo)
      if (file.size > 20 * 1024 * 1024) {
        mostrarAlerta('El archivo supera el tamaño máximo de 20MB', 'danger');
        inputArchivo.value = "";
        return;
      }

      // Validar tipo
      if (file.type !== 'application/pdf') {
        mostrarAlerta('Solo se permiten archivos PDF', 'danger');
        inputArchivo.value = "";
        return;
      }

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

    // Ocultar alertas
    mensajeAlerta.classList.add('d-none');
  }

  // Función para mostrar alertas
  function mostrarAlerta(mensaje, tipo) {
    mensajeAlerta.textContent = mensaje;
    mensajeAlerta.className = `alert alert-${tipo}`;
    mensajeAlerta.classList.remove('d-none');

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
      mensajeAlerta.classList.add('d-none');
    }, 5000);
  }

  // Submit del formulario con AJAX
  document.getElementById('form-reporte').addEventListener('submit', function(e) {
    e.preventDefault();

    // Validar que se haya seleccionado un número de reporte
    const numeroReporte = document.getElementById('numero_reporte').value;
    if (!numeroReporte) {
      mostrarAlerta('Por favor seleccione el número de reporte', 'warning');
      return;
    }

    // Validar fechas
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;
    if (!fechaInicio || !fechaFin) {
      mostrarAlerta('Por favor ingrese el periodo completo', 'warning');
      return;
    }

    if (new Date(fechaFin) < new Date(fechaInicio)) {
      mostrarAlerta('La fecha de fin debe ser posterior o igual a la fecha de inicio', 'warning');
      return;
    }

    // Validar resumen
    const resumen = document.getElementById('resumen').value.trim();
    if (!resumen) {
      mostrarAlerta('Por favor ingrese el resumen de actividades', 'warning');
      return;
    }

    // Deshabilitar botón de envío
    btnEnviar.disabled = true;
    btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

    // Preparar FormData
    const formData = new FormData(this);

    // Enviar con AJAX
    fetch('{{ route("alumno.reportes.store") }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        mostrarAlerta(data.message || 'Reporte enviado exitosamente', 'success');

        // Limpiar formulario después de 2 segundos
        setTimeout(() => {
          resetFormulario();
          // Opcional: redirigir a la lista de reportes
          // window.location.href = '{{ route("alumno.reportes.lista") }}';
        }, 2000);
      } else {
        mostrarAlerta(data.error || 'Error al enviar el reporte', 'danger');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      mostrarAlerta('Error al enviar el reporte. Por favor intente de nuevo.', 'danger');
    })
    .finally(() => {
      // Rehabilitar botón
      btnEnviar.disabled = false;
      btnEnviar.innerHTML = '<i class="bi bi-send"></i> Enviar';
    });
  });

  // Drag and drop
  zonaSubida.addEventListener('dragover', (e) => {
    e.preventDefault();
    zonaSubida.classList.add('border-primary');
  });

  zonaSubida.addEventListener('dragleave', () => {
    zonaSubida.classList.remove('border-primary');
  });

  zonaSubida.addEventListener('drop', (e) => {
    e.preventDefault();
    zonaSubida.classList.remove('border-primary');

    if (e.dataTransfer.files.length > 0) {
      inputArchivo.files = e.dataTransfer.files;
      inputArchivo.dispatchEvent(new Event('change'));
    }
  });
</script>
@endpush
@endsection
