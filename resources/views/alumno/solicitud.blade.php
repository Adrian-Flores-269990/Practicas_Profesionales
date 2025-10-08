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

  <form action="{{ route('solicitud.store') }}" method="POST">
    @csrf

    <div class="accordion" id="soliAccordion">

      {{-- 1. Datos del solicitante --}}
      <div class="accordion-item soli-card">
        <h2 class="accordion-header" id="h-solicitante">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-solicitante" aria-expanded="true">
            Datos generales del solicitante
          </button>
        </h2>
        <div id="sec-solicitante" class="accordion-collapse collapse show" data-bs-parent="#soliAccordion">
          <div class="accordion-body row g-3">
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

      {{-- 2. Sector y empresa --}}
        <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-sector">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-sector">
            Sector y datos de la empresa
            </button>
        </h2>

        <div id="sec-sector" class="accordion-collapse collapse" data-bs-parent="#soliAccordion">
            <div class="accordion-body">
            <div class="row g-3">
                {{-- Tipo de sector --}}
                <div class="col-md-4">
                <label class="form-label">Tipo de sector</label>
                <select name="sector" id="sector" class="form-select mt-1" required>
                    <option value="">Seleccione...</option>
                    <option value="privado">Privado</option>
                    <option value="publico">Público</option>
                    <option value="uaslp">UASLP</option>
                </select>
                </div>

                {{-- Campos comunes --}}
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

            {{-- SECCIONES ESPECÍFICAS DE SECTOR --}}
            <hr class="my-4">

            {{-- SECTOR PRIVADO --}}
            <div id="sector-privado" class="sector-section d-none row g-3">
                <h5 class="text-primary fw-bold">Sector Privado</h5>
                <div class="col-md-6">
                <label class="form-label">Número de trabajadores</label>
                <select name="num_trabajadores" class="form-select">
                    <option value="">Seleccione...</option>
                    <option value="1">Micro (1–30)</option>
                    <option value="2">Pequeña (31–100)</option>
                    <option value="3">Mediana (101–250)</option>
                    <option value="4">Grande (+250)</option>
                </select>
                </div>
                <div class="col-md-6">
                <label class="form-label">Actividad o giro</label>
                <select name="actividad_giro" class="form-select">
                    <option value="">Seleccione...</option>
                    <option value="1">Extractiva</option>
                    <option value="2">Manufacturera</option>
                    <option value="3">Comercial</option>
                    <option value="4">Comisionista</option>
                    <option value="5">Servicio</option>
                </select>
                </div>
            </div>

            {{-- SECTOR PÚBLICO --}}
            <div id="sector-publico" class="sector-section d-none row g-3">
                <h5 class="text-primary fw-bold">Sector Público</h5>
                <div class="col-md-6">
                <label class="form-label">Ámbito</label>
                <select name="ambito" class="form-select">
                    <option value="">Seleccione...</option>
                    <option value="1">Municipal</option>
                    <option value="2">Estatal</option>
                    <option value="3">Federal</option>
                </select>
                </div>
            </div>

            {{-- SECTOR UASLP --}}
            <div id="sector-uaslp" class="sector-section d-none row g-3">
                <h5 class="text-primary fw-bold">Sector UASLP</h5>
                <div class="col-md-6">
                <label class="form-label">Tipo de entidad</label>
                <select name="tipo_entidad" class="form-select">
                    <option value="">Seleccione...</option>
                    <option value="1">Instituto</option>
                    <option value="2">Centro de Investigación</option>
                </select>
                </div>
                <div class="col-md-6">
                <label class="form-label">Entidad académica</label>
                <input type="text" name="entidad_academica" class="form-control">
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
            <div id="sec-empresa" class="accordion-collapse collapse">
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
            <div id="sec-encargado" class="accordion-collapse collapse">
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
            <div id="sec-proyecto" class="accordion-collapse collapse">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-12"><label class="form-label">Título del proyecto</label><input name="titulo_proyecto" class="form-control" required></div>
                  <div class="col-12"><label class="form-label">Actividades</label><textarea name="actividades" class="form-control" rows="4" required></textarea></div>
                </div>
              </div>
            </div>
          </div>

          {{-- 6. Horario --}}
          <div class="accordion-item soli-card mt-3">
            <h2 class="accordion-header" id="h-horario">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-horario">
                Horario
              </button>
            </h2>
            <div id="sec-horario" class="accordion-collapse collapse">
              <div class="accordion-body">
                <div class="row g-2">
                  <div class="col-md-3"><label class="form-label">Horas por semana</label><input type="number" name="horas_semana" class="form-control" required></div>
                  <div class="col-md-3">
                    <label class="form-label">Turno</label>
                    <select name="turno" class="form-select" required>
                      <option value="1">Matutino</option>
                      <option value="0">Vespertino</option>
                      <option value="2">Mixto</option>
                    </select>
                  </div>
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
            <div id="sec-creditos" class="accordion-collapse collapse">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-md-4"><label class="form-label">Créditos a solicitar</label><input type="number" name="creditos" class="form-control" required></div>
                  <div class="col-md-4"><label class="form-label">Apoyo económico</label><input type="text" name="apoyo_economico" class="form-control" placeholder="$" required></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-3 d-flex justify-content-end gap-2">
          <a href="{{ route('alumno.inicio') }}" class="btn btn-danger">Cancelar</a>
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
  const sectorSelect = document.getElementById('sector');
  const sectorSections = document.querySelectorAll('.sector-section');

  // Mostrar solo la sección del sector seleccionado
  sectorSelect.addEventListener('change', () => {
    sectorSections.forEach(sec => sec.classList.add('d-none'));
    const selected = document.getElementById('sector-' + sectorSelect.value);
    if (selected) selected.classList.remove('d-none');
  });

  // Colorear secciones completas
  function checkSection(section) {
    const inputs = section.querySelectorAll('input, select, textarea');
    let completed = true;
    inputs.forEach(input => {
      if (input.hasAttribute('required') && !input.value) completed = false;
    });
    const header = section.querySelector('.accordion-header button');
    header.style.backgroundColor = completed ? '#4583B7' : '';
    header.style.color = completed ? 'white' : '';
  }

  accordionItems.forEach(item => {
    const inputs = item.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
      input.addEventListener('input', () => checkSection(item));
      input.addEventListener('change', () => checkSection(item));
    });
    checkSection(item);
  });

  // Validación al enviar
  form.addEventListener('submit', function(e) {
    let allValid = true;
    accordionItems.forEach(item => {
      const inputs = item.querySelectorAll('input[required], select[required], textarea[required]');
      inputs.forEach(input => {
        if (!input.value) allValid = false;
      });
    });
    if (!allValid) {
      e.preventDefault();
      alert('Debe completar todas las secciones antes de enviar.');
    }
  });
});
</script>
@endpush
