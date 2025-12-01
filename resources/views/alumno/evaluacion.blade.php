@extends('layouts.alumno')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@section('title','Evaluación de la Empresa')

@push('styles')
<style>

/* CONTENEDOR PRINCIPAL DE PREGUNTAS */
.evaluacion-wrapper {
    display: flex;
    flex-direction: column;
    gap: 28px; /* más separación entre preguntas */
}

/* 🔵 TARJETA PARA PREGUNTAS DE SELECCIÓN (compacta) */
.evaluacion-item {
    padding: 20px 24px; /* más padding interno */
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #dce2ec;
    transition: all .25s ease;
}

.evaluacion-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* 🔵 TARJETA PARA PREGUNTAS ABIERTAS */
.evaluacion-textarea-box {
    padding: 24px 26px; /* más padding para dar aire */
    background: #ffffff;
    border-radius: 12px;
    border: 2px solid #cfd7e6;
    transition: all .25s ease;
}

.evaluacion-textarea-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 14px rgba(0,0,0,0.10);
}

/* TITULO DE PREGUNTA */
.evaluacion-label {
    font-size: 16px; /* más grande */
    font-weight: 600;
    margin-bottom: 14px; /* MÁS separación entre pregunta y respuesta */
    color: #002b5c;
}

/* SELECT MODERNO */
.select-evaluacion {
    width: 260px;
    padding: 12px 14px;
    border: 2px solid #0d6efd;
    border-radius: 10px;
    font-size: 15px;
    transition: .25s ease;
    appearance: none;
    background:
        #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath fill='%23003b82' d='M4 6l4 4 4-4z'/%3E%3C/svg%3E")
        no-repeat right 12px center;
}

.select-evaluacion:focus {
    border-color: #004bb5;
    box-shadow: 0 0 0 3px rgba(0,91,200,0.3);
}

/* TEXTAREA */
.textarea-evaluacion {
    width: 100%;
    padding: 18px;
    border-radius: 10px;
    border: 2px solid #b6c3d8;
    font-size: 15px;
    resize: none;
    transition: .25s ease;
}

.textarea-evaluacion:focus {
    border-color: #004bb5;
    box-shadow: 0 0 0 4px rgba(0,91,200,0.2);
    outline: none;
}

/* Animación suave para ocultar alertas automáticamente */
.alert.fade-out {
    opacity: 0;
    transition: opacity 0.6s ease;
}

</style>
@endpush

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
      <div class="evaluacion-wrapper">
      @foreach($preguntas as $index => $pregunta)
          {{-- PREGUNTAS DE SELECCIÓN --}}
          @if($pregunta->Id_Catalogo_Opcion == 1)
              <div class="evaluacion-item">
                  <label class="evaluacion-label">
                      {{ $index + 1 }}. {{ $pregunta->Pregunta }} <span class="text-danger">*</span>
                  </label>

                  <select class="select-evaluacion" 
                          name="respuestas[{{ $pregunta->Id_Pregunta }}]"
                          required>
                      <option value="">Seleccione una opción</option>
                      <option value="1">1 - Ninguna</option>
                      <option value="2">2 - Poca</option>
                      <option value="3">3 - Regular</option>
                      <option value="4">4 - Mucha</option>
                  </select>
              </div>

          {{-- PREGUNTAS DE TEXTO --}}
          @elseif($pregunta->Id_Catalogo_Opcion == 2)

              <div class="evaluacion-textarea-box">
                  <label class="evaluacion-label">
                      {{ $index + 1 }}. {{ $pregunta->Pregunta }} <span class="text-danger">*</span>
                  </label>

                  <textarea 
                      class="textarea-evaluacion"
                      rows="4"
                      maxlength="200"
                      name="respuestas[{{ $pregunta->Id_Pregunta }}]"
                      required
                      placeholder="Escribe tu respuesta aquí..."
                  ></textarea>

                  <div class="textarea-contador">Máximo 200 caracteres</div>
              </div>
          @endif
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

/* ============================================================
   SUBMIT PRINCIPAL — SOLO ABRE EL MODAL
   ============================================================ */
document.getElementById('form-evaluacion').addEventListener('submit', function (e) {
    e.preventDefault();

    // VALIDAR RESPUESTAS
    const form = this;
    const selects = form.querySelectorAll('select[required]');
    const textareas = form.querySelectorAll('textarea[required]');

    let todasRespondidas = true;
    let preguntasSinResponder = [];

    selects.forEach((select, idx) => {
        if (!select.value) {
            todasRespondidas = false;
            preguntasSinResponder.push(`Pregunta ${idx + 1}`);
        }
    });

    textareas.forEach((textarea, idx) => {
        if (!textarea.value.trim()) {
            todasRespondidas = false;
            preguntasSinResponder.push(`Pregunta ${selects.length + idx + 1}`);
        }
    });

    if (!todasRespondidas) {
        mostrarAlerta('Por favor responde todas las preguntas antes de enviar.', 'warning');
        return;
    }

    // 🚀 ABRIR MODAL BONITO
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEnvio'));
    modal.show();
});

/* ============================================================
   BOTÓN DEL MODAL — AQUÍ SÍ SE ENVÍA LA EVALUACIÓN
   ============================================================ */
document.getElementById('btnConfirmarEnvio').onclick = function () {
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEnvio'));
    modal.hide();
    enviarEvaluacion();
};

/* ============================================================
   FUNCIÓN PARA ENVIAR EVALUACIÓN
   ============================================================ */
function enviarEvaluacion() {

    const form = document.getElementById('form-evaluacion');
    const btnEnviar = document.getElementById('btn-enviar');

    btnEnviar.disabled = true;
    btnEnviar.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Enviando...';

    const data = { respuestas: {} };

    // RECOLECTAR RESPUESTAS
    const respuestasInputs = form.querySelectorAll('select[name^="respuestas"], textarea[name^="respuestas"]');

    respuestasInputs.forEach(input => {
        const match = input.name.match(/respuestas\[(\d+)\]/);
        if (match && input.value) {

            const idPregunta = match[1];

            data.respuestas[idPregunta] = 
                input.tagName === 'SELECT'
                    ? input.options[input.selectedIndex].text
                    : input.value.trim();
        }
    });

    fetch('{{ route("alumno.evaluacion.guardar") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {

        if (res.success) {
            window.location.href = res.redirect;
        } else {
            mostrarAlerta(res.error || 'Error al guardar la evaluación', 'danger');
            btnEnviar.disabled = false;
            btnEnviar.innerHTML = '<i class="bi bi-send me-2"></i>Enviar Evaluación';
        }
    })
    .catch(err => {
        console.error(err);
        mostrarAlerta('Error al enviar la evaluación.', 'danger');
        btnEnviar.disabled = false;
        btnEnviar.innerHTML = '<i class="bi bi-send me-2"></i>Enviar Evaluación';
    });
}

/* ============================================================
   ALERTAS BONITAS — AUTO OCULTAR
   ============================================================ */
function mostrarAlerta(mensaje, tipo) {
    const alertContainer = document.getElementById('alert-container');
    const iconos = {
        success: 'check-circle-fill',
        danger: 'exclamation-triangle-fill',
        warning: 'exclamation-circle-fill',
        info: 'info-circle-fill'
    };

    const alerta = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            <i class="bi bi-${iconos[tipo]} me-2"></i>${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    alertContainer.innerHTML = alerta;

    setTimeout(() => {
        const al = alertContainer.querySelector('.alert');
        if (al) {
            al.classList.add('fade-out');
            setTimeout(() => al.remove(), 600);
        }
    }, 3000);
}

/* ============================================================
   CONTADOR DE CARACTERES — TEXTAREA
   ============================================================ */
document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
    const max = textarea.getAttribute('maxlength');
    const counter = textarea.nextElementSibling;

    textarea.addEventListener('input', function () {
        const len = this.value.length;
        counter.textContent = `${len}/${max} caracteres`;

        if (len >= max) counter.classList.add('text-danger');
        else counter.classList.remove('text-danger');
    });
});

</script>

@endpush

<!-- MODAL DE CONFIRMACIÓN -->
<div class="modal fade" id="modalConfirmarEnvio" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg" style="border-radius: 12px;">
      
      <div class="modal-header" style="background:#003b82; color:white; border-top-left-radius:12px; border-top-right-radius:12px;">
        <h5 class="modal-title">
          <i class="bi bi-question-circle me-2"></i>
          Confirmar envío
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="mb-0">
          ¿Estás seguro de que deseas enviar la evaluación?<br>
          <small class="text-muted">Una vez enviada, puedes modificarla pero sustituirá la anterior.</small>
        </p>
      </div>

      <div class="modal-footer d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>
          Cancelar
        </button>

        <button type="button" id="btnConfirmarEnvio" class="btn btn-success">
          <i class="bi bi-send me-2"></i>
          Enviar
        </button>
      </div>

    </div>
  </div>
</div>


@endsection
