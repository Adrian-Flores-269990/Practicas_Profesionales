@extends('layouts.encargado')
@section('title','FPP02 - Revisar Registro')

@push('styles')
<style>
  .action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
  }

  .btn-aceptar {
    background: #1f8950ff;
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
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
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-rechazar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245,101,101,0.4);
    color: white;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">

    <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
      PRUEBAAAAAAAA
    </h4>

    <div class="bg-white p-4 rounded shadow-sm w-100">

      @if($pdfPath)
        <div class="mb-4">
          <h6 class="fw-bold">Documento subido:</h6>
          <iframe src="{{ asset($pdfPath) }}" style="border:1px solid #4583B7; display:block; margin:auto; width:816px; height:1100px; max-width:100%; max-height:100%;"></iframe>
          <div class="d-flex gap-2 mt-2">
            <a href="{{ asset($pdfPath) }}" target="_blank" class="btn btn-outline-primary">Abrir PDF en nueva pestaña</a>
          </div>
          
          <form action="{{ route('encargado.calificarRegistro') }}" method="POST" class="d-flex gap-2">
            @csrf
            <input type="hidden" name="seccion" value="solicitante">
            <input type="hidden" name="claveAlumno" value="{{ $claveAlumno }}">

            <button type="submit" name="valor" value="1" class="btn btn-aceptar btn-accion">
                <i class="bi bi-check-lg me-1"></i> Aceptar
            </button>

            <button type="submit" name="valor" value="0" class="btn btn-rechazar btn-accion">
                <i class="bi bi-x-lg me-1"></i> Rechazar
            </button>
          </form>
        </div>
      @endif    
    </div>
  </div>

@endsection

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
</script>
@endpush
