@extends('layouts.encargado')

@section('title','Registrar Nueva Empresa')

@push('styles')
<style>
  .form-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 2rem;
    max-width: 900px;
    margin: 0 auto;
  }
  
  .form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    text-align: center;
  }
  
  .form-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border-left: 4px solid #667eea;
  }
  
  .form-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #495057;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
  }
  
  .required {
    color: #dc3545;
    margin-left: 3px;
  }
  
  .form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.65rem 0.9rem;
  }
  
  .form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102,126,234,.25);
  }
  
  .btn-submit {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
  }
  
  .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(17,153,142,0.4);
    color: white;
  }
  
  .btn-cancel {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
  }
  
  .btn-cancel:hover {
    background: #5a6268;
    color: white;
    transform: translateY(-2px);
  }
  
  .btn-clear {
    background: #ffc107;
    color: #000;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
  }
  
  .btn-clear:hover {
    background: #e0a800;
    color: #000;
    transform: translateY(-2px);
  }
  
  .info-alert {
    background: #e7f3ff;
    border-left: 4px solid #2196f3;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
  }
  
  .char-counter {
    font-size: 0.85rem;
    color: #6c757d;
    text-align: right;
    margin-top: 0.25rem;
  }
  
  textarea.form-control {
    resize: vertical;
    min-height: 100px;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    REGISTRAR NUEVA EMPRESA
  </h4>
  
  <div class="p-4">
    
    <div class="form-container">
      


      <div class="info-alert">
        <i class="bi bi-info-circle-fill me-2"></i>
        <strong>Importante:</strong> Los campos marcados con <span class="required">*</span> son obligatorios. 
        Asegúrese de verificar la información antes de guardar.
      </div>

      <form id="formRegistroEmpresa" action="{{ route('encargado.guardar_empresa') }}" method="POST">
        @csrf

        {{-- Sección: Información General --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-info-circle-fill text-primary"></i>
            Información General
          </div>
          
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label">
                Nombre de la Empresa<span class="required">*</span>
              </label>
              <input 
                type="text" 
                name="nombre" 
                class="form-control" 
                placeholder="Ej: Tecnologías Innovadoras S.A. de C.V."
                required
              >
            </div>
          </div>
        </div>

        {{-- Sección: Dirección --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-geo-alt-fill text-danger"></i>
            Dirección Completa
          </div>
          
          <div class="row g-3">
            <div class="col-md-12 mb-2">
              <label class="form-label">
                Dirección Completa<span class="required">*</span>
              </label>
              <input 
                type="text" 
                name="direccion" 
                class="form-control" 
                placeholder="Dirección completa de la empresa"
                required
              >
              <small class="text-muted">Ingrese la dirección completa en un solo campo</small>
            </div>

            <div class="col-md-8">
              <label class="form-label">
                Calle<span class="required">*</span>
              </label>
              <input 
                type="text" 
                name="calle" 
                class="form-control" 
                placeholder="Ej: Av. Manuel J. Clouthier"
                required
              >
            </div>

            <div class="col-md-4">
              <label class="form-label">
                Número<span class="required">*</span>
              </label>
              <input 
                type="text" 
                name="numero" 
                class="form-control" 
                placeholder="Ej: 1234"
                required
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">
                Colonia<span class="required">*</span>
              </label>
              <input 
                type="text" 
                name="colonia" 
                class="form-control" 
                placeholder="Ej: Lomas del Tecnológico"
                required
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">
                Código Postal<span class="required">*</span>
              </label>
              <input 
                type="text" 
                name="codigo_postal" 
                class="form-control" 
                placeholder="Ej: 78216"
                pattern="[0-9]{5}"
                maxlength="5"
                required
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">
                Ramo/Giro<span class="required">*</span>
              </label>
              <select name="ramo" class="form-select" required>
                <option value="">Seleccione un ramo...</option>
                <option value="tecnologia">Tecnología</option>
                <option value="construccion">Construcción</option>
                <option value="manufactura">Manufactura</option>
                <option value="servicios">Servicios</option>
                <option value="comercio">Comercio</option>
                <option value="educacion">Educación</option>
                <option value="salud">Salud</option>
                <option value="otro">Otro</option>
              </select>
            </div>
          </div>
        </div>

        {{-- Sección: Contacto --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-telephone-fill text-success"></i>
            Información de Contacto
          </div>
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">
                Teléfono<span class="required">*</span>
              </label>
              <input 
                type="tel" 
                name="telefono" 
                class="form-control" 
                placeholder="Ej: 4441234567"
                pattern="[0-9]{10}"
                maxlength="10"
                required
              >
              <small class="text-muted">10 dígitos sin espacios ni guiones</small>
            </div>
          </div>
        </div>


        {{-- Botones de acción --}}
        <div class="d-flex justify-content-end gap-3 mt-4">
          <button type="button" class="btn btn-clear" onclick="limpiarFormulario()">
            <i class="bi bi-eraser-fill me-2"></i>
            Limpiar
          </button>
          <a href="{{ route('encargado.inicio') }}" class="btn btn-cancel">
            <i class="bi bi-x-circle me-2"></i>
            Cancelar
          </a>
          <button type="submit" class="btn btn-submit">
            <i class="bi bi-check-circle-fill me-2"></i>
            Guardar Empresa
          </button>
        </div>

      </form>

    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
  // Contador de caracteres para comentarios
  const comentarios = document.getElementById('comentarios');
  const charCount = document.getElementById('charCount');

  comentarios?.addEventListener('input', function() {
    charCount.textContent = this.value.length;
  });

  // Limpiar formulario
  function limpiarFormulario() {
    if (confirm('¿Estás seguro de que deseas limpiar todos los campos?')) {
      document.getElementById('formRegistroEmpresa').reset();
      charCount.textContent = '0';
    }
  }

  // Validación antes de enviar
  document.getElementById('formRegistroEmpresa')?.addEventListener('submit', function(e) {
    const telefono = document.querySelector('input[name="telefono"]').value;
    const cp = document.querySelector('input[name="codigo_postal"]').value;
    
    // Validar teléfono
    if (telefono.length !== 10 || !/^\d+$/.test(telefono)) {
      e.preventDefault();
      alert('❌ El teléfono debe contener exactamente 10 dígitos.');
      return false;
    }
    
    // Validar código postal
    if (cp.length !== 5 || !/^\d+$/.test(cp)) {
      e.preventDefault();
      alert('❌ El código postal debe contener exactamente 5 dígitos.');
      return false;
    }
    
    // Confirmación final
    if (!confirm('¿Confirmar el registro de esta empresa?')) {
      e.preventDefault();
      return false;
    }
  });

  // Formato automático de teléfono (solo números)
  document.querySelector('input[name="telefono"]')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
  });

  // Formato automático de código postal (solo números)
  document.querySelector('input[name="codigo_postal"]')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
  });
</script>
@endpush