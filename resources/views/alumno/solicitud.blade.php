@extends('layouts.alumno')
@section('title','Registro de solicitud de prácticas profesionales')

@push('styles')
<style>
  .form-label { font-weight: 700; }
</style>
@endpush

@section('content')
@include('partials.nav.registro')

<div class="container-fluid py-3">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    SOLICITUD DE REGISTRO DEL ALUMNO
  </h4>

  <div class="row g-3">
    <section class="col-12">

      <form action="{{ route('solicitud.store') }}" method="POST">
        @csrf

        <div class="accordion" id="soliAccordion">

          {{-- 1. Solicitante --}}
          <div class="accordion-item soli-card">
            <h2 class="accordion-header" id="h-solicitante">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-solicitante" aria-expanded="true">
                Datos generales del solicitante
              </button>
            </h2>
            <div id="sec-solicitante" class="accordion-collapse collapse show" data-bs-parent="#soliAccordion" aria-labelledby="h-solicitante">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Matrícula</label>
                    <input type="text" name="matricula" class="form-control" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="nombre" class="form-control" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Programa</label>
                    <input type="text" name="programa" class="form-control" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Semestre</label>
                    <input type="number" name="semestre" min="1" max="12" class="form-control" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Email UASLP</label>
                    <input type="email" name="email" class="form-control" required>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- 2. Prácticas profesionales --}}
          <div class="accordion-item soli-card mt-3">
            <h2 class="accordion-header" id="h-practicas">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-practicas">
                Prácticas profesionales – Sector público/privado/UASLP
              </button>
            </h2>
            <div id="sec-practicas" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-practicas">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-md-3">
                    <label class="form-label">Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control mt-1" required>
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Fecha de término</label>
                    <input type="date" name="fecha_termino" class="form-control mt-1" required>
                  </div>

                  <div class="col-12">
                    <span class="form-label">Tipo de sector</span>
                    <div class="d-inline-flex align-items-center gap-3 ms-2" required>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sector" value="municipal">
                        <label class="form-check-label">Municipal</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sector" value="estatal">
                        <label class="form-check-label">Estatal</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sector" value="federal">
                        <label class="form-check-label">Federal</label>
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <label class="form-label">Nombre de la dependencia</label>
                    <input type="text" name="dependencia" class="form-control mt-1" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Calle</label>
                    <input type="text" name="calle" class="form-control mt-1" required>
                  </div>

                  <div class="col-md-2">
                    <label class="form-label">No.</label>
                    <input type="text" name="numero" class="form-control mt-1" required>
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Colonia</label>
                    <input type="text" name="colonia" class="form-control mt-1" required>
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Código Postal</label>
                    <input type="text" name="cp" class="form-control mt-1" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <input type="text" name="estado" class="form-control mt-1" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Municipio</label>
                    <input type="text" name="municipio" class="form-control mt-1" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Área o Departamento</label>
                    <input type="text" name="area" class="form-control mt-1" required>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- 3. Empresa --}}
          <div class="accordion-item soli-card mt-3">
            <h2 class="accordion-header" id="h-empresa">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-empresa">
                Perfil de la empresa
              </button>
            </h2>
            <div id="sec-empresa" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-empresa">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Razón social</label>
                    <input type="text" name="razon_social" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">RFC</label>
                    <input type="text" name="rfc" class="form-control" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion_empresa" class="form-control" rows="3" required></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- 4. Encargado --}}
          <div class="accordion-item soli-card mt-3">
            <h2 class="accordion-header" id="h-encargado">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-encargado">
                Encargado de prácticas / Asesor externo
              </button>
            </h2>
            <div id="sec-encargado" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-encargado">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-md-6"><label class="form-label">Nombre</label><input name="encargado_nombre" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Puesto</label><input name="encargado_puesto" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="encargado_correo" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Teléfono</label><input name="encargado_telefono" class="form-control" required></div>
                </div>
              </div>
            </div>
          </div>

          {{-- 5. Proyecto --}}
          <div class="accordion-item soli-card mt-3">
            <h2 class="accordion-header" id="h-proyecto">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-proyecto">
                Proyecto y actividades
              </button>
            </h2>
            <div id="sec-proyecto" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-proyecto">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-12"><label class="form-label">Título del proyecto</label><input name="titulo_proyecto" class="form-control" required></div>
                  <div class="col-12"><label class="form-label">Actividades</label><textarea name="actividades" class="form-control" rows="4" required></textarea></div>
                </div>
              </div>
            </div>
          </div>

<<<<<<< HEAD
          {{-- 6. Horario --}}
          <div class="accordion-item soli-card mt-3">
            <h2 class="accordion-header" id="h-horario">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-horario">
                Horario
              </button>
            </h2>
            <div id="sec-horario" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-horario">
              <div class="accordion-body">
                <div class="row g-2">
                  <div class="col-md-3"><label class="form-label">Horas por semana</label><input type="number" name="horas_semana" class="form-control" required></div>
                  <div class="col-md-3"><label class="form-label">Turno</label>
                    <select name="turno" class="form-select" required>
                        <option value="1">Matutino</option>
                        <option value="0">Vespertino</option>
                        <option value="2">Mixto</option>
                    </select>
                  </div>
=======
        {{-- 7. Créditos / Apoyo --}}
        <div class="accordion-item soli-card mt-3 mb-3">
          <h2 class="accordion-header" id="h-creditos">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-creditos">
              Créditos / Apoyo económico
            </button>
          </h2>
          <div id="sec-creditos" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-creditos">
            <div class="accordion-body">
              <form id="f-creditos" class="row g-3">
                <div class="col-md-4"><label class="form-label">Créditos a solicitar</label><input type="number" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Apoyo económico</label><input type="text" class="form-control" placeholder="$"></div>
              
                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                  <a href="{{ route('alumno.inicio') }}" class="btn btn-danger">Cancelar</a>
                  <button type="submit" class="btn btn-success">Enviar</button>
>>>>>>> encabezados-usuarios
                </div>
              </div>
            </div>
          </div>

          {{-- 7. Créditos / Apoyo --}}
          <div class="accordion-item soli-card mt-3 mb-3">
            <h2 class="accordion-header" id="h-creditos">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-creditos">
                Créditos / Apoyo económico
              </button>
            </h2>
            <div id="sec-creditos" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-creditos">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-md-4"><label class="form-label">Créditos a solicitar</label><input type="number" name="creditos" class="form-control" required></div>
                  <div class="col-md-4"><label class="form-label">Apoyo económico</label><input type="text" name="apoyo_economico" class="form-control" placeholder="$" required></div>
                </div>
              </div>
            </div>
          </div>
        </div> {{-- /accordion --}}
            <div class="mt-3 d-flex justify-content-end gap-2">
            <a href="{{ route('alumno.home') }}" class="btn btn-danger">Cancelar</a>
            <button type="submit" class="btn btn-success">Enviar solicitud</button>
        </div>
      </form>
    </section>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accordionItems = document.querySelectorAll('.accordion-item');
    const form = document.querySelector('form');

    // Azul para secciones completadas
    function checkSection(section) {
        const inputs = section.querySelectorAll('input, select, textarea');
        let completed = true;
        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value) {
                completed = false;
            }
        });
        const header = section.querySelector('.accordion-header button');
        if (completed) {
            header.style.backgroundColor = '#4583B7';
            header.style.color = 'white';
        } else {
            header.style.backgroundColor = '';
            header.style.color = '';
        }
    }

    // Revisión al cambiar inputs
    accordionItems.forEach(item => {
        const inputs = item.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', () => checkSection(item));
            input.addEventListener('change', () => checkSection(item));
        });
        checkSection(item);
    });

    // Validación antes de enviar
    form.addEventListener('submit', function(e) {
        let allValid = true;

        accordionItems.forEach(item => {
            const inputs = item.querySelectorAll('input[required], select[required], textarea[required]');
            inputs.forEach(input => {
                if (!input.value) allValid = false;
            });
        });

        if (!allValid) {
            e.preventDefault(); // detener envío
            alert('Debe completar todas las secciones para poder enviar la Solicitud');
            return false;
        }
    });
});
</script>
@endpush
