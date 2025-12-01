@extends('layouts.alumno')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@section('title','Evaluación de la Empresa')

@section('content')
@include('partials.nav.registro')

<div class="container-fluid my-0 p-0">
  <!-- Header -->
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-file-earmark-text me-2"></i>
                EVALUACIÓN DE LA EMPRESA POR EL ALUMNO
            </h4>
        </div>
    </div>
  <div class="bg-white p-4 rounded shadow-sm w-100">
    
    @if($evaluacionExistente)
      <div class="alert alert-success mb-4">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Ya has enviado la evaluación de esta empresa.</strong>
        <p class="mb-0 mt-2">Si deseas modificar tus respuestas, puedes llenar el formulario nuevamente y hacer clic en "Enviar".</p>
      </div>
    @endif

    <form id="form-evaluacion">
      @csrf
      
      {{-- Datos Generales con fondo gris clarito --}}
      <div class="bg-light p-3 rounded mb-4">
        <div class="mb-3">
          <label class="form-label fw-bold">Nombre de la empresa:</label>
          <p class="form-control-plaintext">{{ $empresa ?? 'No especificada' }}</p>
        </div>
        <div>
          <label class="form-label fw-bold">Actividad principal que realizó el Alumno:</label>
          <p class="form-control-plaintext">{{ $actividadPrincipal }}</p>
        </div>
      </div>

      {{-- Indicador de escala --}}
      <div class="mb-3">
        <span class="fw-semibold text-danger">
          <i class="bi bi-info-circle me-2"></i>
          Para las preguntas de escala: 1 = Ninguna, 2 = Poca, 3 = Regular, 4 = Mucha
        </span>
      </div>

      {{-- Contenedor de preguntas dinámicas --}}
      <div class="bg-light p-3 rounded mb-4">
        @foreach($preguntas as $index => $pregunta)
          <div class="mb-4">
            <label class="form-label fw-bold">
              {{ ($index + 1) }}. {{ $pregunta->Pregunta }}
              <span class="text-danger">*</span>
            </label>
            
            @if($pregunta->Id_Catalogo_Opcion == 1)
              {{-- Pregunta de escala (1-4) --}}
              <div class="row align-items-center">
                <div class="col-md-3">
                  <select class="form-select" name="respuestas[{{ $pregunta->Id_Pregunta }}]" required>
                    <option value="">Seleccione una opción</option>
                    <option value="1">1 - Ninguna</option>
                    <option value="2">2 - Poca</option>
                    <option value="3">3 - Regular</option>
                    <option value="4">4 - Mucha</option>
                  </select>
                </div>
              </div>
            @elseif($pregunta->Id_Catalogo_Opcion == 2)
              {{-- Pregunta abierta --}}
              <textarea 
                class="form-control" 
                name="respuestas[{{ $pregunta->Id_Pregunta }}]" 
                rows="3" 
                maxlength="200" 
                placeholder="Escribe tu respuesta aquí (máximo 200 caracteres)..."
                required
              ></textarea>
              <div class="form-text">Máximo 200 caracteres</div>
            @endif
          </div>
        @endforeach
      </div>

      {{-- Mensajes de alerta --}}
      <div id="alert-container"></div>

      {{-- Botones --}}
      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-danger" onclick="resetEvaluacion()">
          <i class="bi bi-x-circle me-2"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-success" id="btn-enviar">
          <i class="bi bi-send me-2"></i>Enviar Evaluación
        </button>
      </div>

    </form>
  </div>
</div>

@push('scripts')
<script>
  function resetEvaluacion() {
    if (confirm('¿Estás seguro de que deseas cancelar? Se perderán todos los cambios.')) {
      document.getElementById('form-evaluacion').reset();
    }
  }

  // Manejar el envío del formulario
  document.getElementById('form-evaluacion').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const btnEnviar = document.getElementById('btn-enviar');
    const alertContainer = document.getElementById('alert-container');
    
    // Validar que todas las preguntas estén respondidas
    let todasRespondidas = true;
    let preguntasSinResponder = [];
    
    const selects = form.querySelectorAll('select[required]');
    const textareas = form.querySelectorAll('textarea[required]');
    
    selects.forEach((select, idx) => {
      if (!select.value) {
        todasRespondidas = false;
        preguntasSinResponder.push(`Pregunta ${idx + 1} (selección)`);
      }
    });
    
    textareas.forEach((textarea, idx) => {
      if (!textarea.value.trim()) {
        todasRespondidas = false;
        preguntasSinResponder.push(`Pregunta ${selects.length + idx + 1} (texto)`);
      }
    });
    
    if (!todasRespondidas) {
      console.log('Preguntas sin responder:', preguntasSinResponder);
      mostrarAlerta('Por favor, responde todas las preguntas antes de enviar.', 'warning');
      return;
    }
    
    // Confirmar envío
    if (!confirm('¿Estás seguro de que deseas enviar la evaluación? Una vez enviada, puedes modificarla pero se reemplazará la anterior.')) {
      return;
    }
    
    // Deshabilitar botón y mostrar loading
    btnEnviar.disabled = true;
    btnEnviar.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Enviando...';
    
    // Construir objeto con las respuestas correctamente estructuradas
    const data = {
      respuestas: {}
    };
    
    // Recopilar todas las respuestas (selects y textareas)
    const respuestasInputs = form.querySelectorAll('select[name^="respuestas"], textarea[name^="respuestas"]');
    respuestasInputs.forEach(input => {
      const name = input.getAttribute('name');
      // Extraer el ID de pregunta de: respuestas[123]
      const match = name.match(/respuestas\[(\d+)\]/);
      if (match && input.value) {
        const idPregunta = match[1];
        
        // Si es un select, guardamos el texto de la opción seleccionada (ej: "1 - Ninguna")
        if (input.tagName === 'SELECT') {
          const selectedOption = input.options[input.selectedIndex];
          data.respuestas[idPregunta] = selectedOption.text;
        } else {
          // Si es textarea, guardamos el texto tal cual
          data.respuestas[idPregunta] = input.value.trim();
        }
      }
    });
    
    console.log('Datos a enviar:', data); // Debug
    console.log('Total respuestas:', Object.keys(data.respuestas).length); // Debug
    
    // Enviar formulario
    fetch('{{ route("alumno.evaluacion.guardar") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        mostrarAlerta(data.message, 'success');
        
        // Recargar página después de 2 segundos
        setTimeout(() => {
          location.reload();
        }, 2000);
      } else {
        mostrarAlerta(data.error || 'Error al guardar la evaluación', 'danger');
        btnEnviar.disabled = false;
        btnEnviar.innerHTML = '<i class="bi bi-send me-2"></i>Enviar Evaluación';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      mostrarAlerta('Error al enviar la evaluación. Por favor, intenta de nuevo.', 'danger');
      btnEnviar.disabled = false;
      btnEnviar.innerHTML = '<i class="bi bi-send me-2"></i>Enviar Evaluación';
    });
  });
  
  function mostrarAlerta(mensaje, tipo) {
    const alertContainer = document.getElementById('alert-container');
    const iconos = {
      'success': 'check-circle-fill',
      'danger': 'exclamation-triangle-fill',
      'warning': 'exclamation-circle-fill',
      'info': 'info-circle-fill'
    };
    
    const alerta = `
      <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
        <i class="bi bi-${iconos[tipo]} me-2"></i>
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;
    
    alertContainer.innerHTML = alerta;
    
    // Scroll a la alerta
    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Auto-ocultar después de 5 segundos (excepto success que se recarga la página)
    if (tipo !== 'success') {
      setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
          alert.classList.remove('show');
          setTimeout(() => {
            alertContainer.innerHTML = '';
          }, 150);
        }
      }, 5000);
    }
  }
  
  // Contador de caracteres para textareas
  document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
    const maxLength = textarea.getAttribute('maxlength');
    const formText = textarea.nextElementSibling;
    
    textarea.addEventListener('input', function() {
      const currentLength = this.value.length;
      formText.textContent = `${currentLength}/${maxLength} caracteres`;
      
      if (currentLength >= maxLength) {
        formText.classList.add('text-danger');
      } else {
        formText.classList.remove('text-danger');
      }
    });
  });
</script>
@endpush

@endsection
