<div class="p-3">
  {{-- Información del reporte --}}
  <div class="info-section mb-3">
    <h6 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Información del Reporte #{{ $reporte->Numero_Reporte }}</h6>
    
    <div class="row g-3">
      <div class="col-md-6">
        <strong>Periodo:</strong>
        <div>{{ \Carbon\Carbon::parse($reporte->Periodo_Ini)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reporte->Periodo_Fin)->format('d/m/Y') }}</div>
      </div>
      
      <div class="col-md-6">
        <strong>Tipo:</strong>
        <div>
          @if($reporte->Reporte_Final)
            <span class="badge bg-info">Reporte Final</span>
          @else
            <span class="badge bg-secondary">Reporte Parcial</span>
          @endif
        </div>
      </div>
      
      <div class="col-12">
        <strong>Resumen de actividades:</strong>
        <div class="mt-2 p-3" style="background: #f8f9fa; border-radius: 6px; max-height: 200px; overflow-y: auto;">
          {{ $reporte->Resumen_Actividad }}
        </div>
      </div>
      
      @if($reporte->Archivo_Agregado && $reporte->Nombre_Archivo)
        <div class="col-12">
          <strong>Archivo adjunto:</strong>
          <div class="mt-2">
            <a href="{{ route('encargado.reportes.descargar', $reporte->Id_Reporte) }}" 
               class="btn btn-outline-danger" 
               target="_blank">
              <i class="bi bi-file-earmark-pdf me-2"></i>{{ $reporte->Nombre_Archivo }}
              <i class="bi bi-download ms-2"></i>
            </a>
          </div>
        </div>
      @endif
    </div>
  </div>

  <hr>

  {{-- Formulario de calificación --}}
  @if($reporte->Calificacion !== null)
    {{-- Mostrar calificación existente --}}
    <div class="alert alert-success">
      <h6><i class="bi bi-check-circle me-2"></i>Este reporte ya ha sido calificado</h6>
      
      <div class="row mt-3">
        <div class="col-md-4">
          <strong>Calificación:</strong>
          <div class="fs-3 text-success">{{ $reporte->Calificacion }}</div>
        </div>
        
        @if($reporte->Observaciones)
          <div class="col-md-8">
            <strong>Observaciones:</strong>
            <div class="mt-2 p-3" style="background: white; border-radius: 6px;">
              {{ $reporte->Observaciones }}
            </div>
          </div>
        @endif
      </div>
    </div>
  @else
    {{-- Formulario para calificar --}}
    <h6 class="mb-3"><i class="bi bi-pencil-square me-2"></i>Calificar Reporte</h6>
    
    <div class="row g-3">
      <div class="col-md-4">
        <label for="calificacion" class="form-label">Calificación (0-100) <span class="text-danger">*</span></label>
        <input 
          type="number" 
          class="form-control" 
          id="calificacion" 
          min="0" 
          max="100" 
          step="0.01"
          placeholder="Ej: 85"
          required
        >
      </div>
      
      <div class="col-md-8">
        <label for="observaciones" class="form-label">Observaciones (opcional)</label>
        <textarea 
          class="form-control" 
          id="observaciones" 
          rows="3"
          maxlength="1000"
          placeholder="Comentarios o sugerencias para el alumno..."
        ></textarea>
        <div class="form-text">Máximo 1000 caracteres</div>
      </div>
      
      <div class="col-12 text-end">
        <button 
          type="button" 
          class="btn btn-success btn-lg"
          onclick="calificarReporte({{ $reporte->Id_Reporte }})"
        >
          <i class="bi bi-check-circle me-2"></i>Guardar calificación
        </button>
      </div>
    </div>
  @endif
</div>

<style>
  .info-section {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
  }
</style>
