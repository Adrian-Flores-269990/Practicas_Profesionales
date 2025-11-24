@extends('layouts.encargado')
@section('title', 'Revisión de Carta de Presentación')

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
    color: #0a2f6eff;
    font-size: 1rem;
    font-weight: 500;
  }

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
    REVISAR CARTA DE PRESENTACIÓN (ENCARGADO)
  </h4>

  <div class="container py-4">

    <form action="{{ route('encargado.cartaPresentacion.accion') }}" method="POST" id="form-revision">
      @csrf
      <input type="hidden" name="claveAlumno" value="{{ $claveAlumno }}">

      {{-- PREVIEW DEL PDF --}}
      <div class="seccion-card">
        <div class="seccion-header">
          <i class="bi bi-file-earmark-pdf-fill"></i>
          Carta de Presentación
        </div>
        <div class="seccion-body">
          @if($pdfPath)
            <iframe src="{{ asset($pdfPath) }}" width="100%" height="500px" style="border:1px solid #4583B7; border-radius:8px;"></iframe>
            <div class="d-flex gap-2 mt-2">
              <a href="{{ asset($pdfPath) }}" target="_blank" class="btn-open-pdf">
                <i class="bi bi-box-arrow-up-right"></i>
                Abrir en nueva pestaña
              </a>
            </div>
          @else
            <div class="alert alert-warning mb-0">
              <i class="bi bi-exclamation-triangle me-2"></i>
              No existe archivo de carta generado.
            </div>
          @endif
        </div>
      </div>

      {{-- BOTONES FINALES --}}
      <div class="mt-4 text-center d-flex gap-3 justify-content-center">
        <button type="submit" name="accion" value="aprobar" class="btn-aceptar">
          <i class="bi bi-check-circle-fill me-2"></i>
          Aceptar
        </button>

        <button type="submit" name="accion" value="rechazar" class="btn-rechazar">
          <i class="bi bi-x-circle-fill me-2"></i>
          Rechazar
        </button>

        <a href="{{ route('encargado.cartasPresentacion') }}" class="btn-regresar">
          <i class="bi bi-arrow-left me-2"></i>
          Volver al listado
        </a>
      </div>
    </form>
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
