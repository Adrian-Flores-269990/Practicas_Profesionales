@extends('layouts.administrador')
@section('title', 'Encargados de Prácticas')

@section('content')

<h4 class="text-center fw-bold text-white py-3 shadow-sm"
    style="background: linear-gradient(90deg, #00124E, #003B95);">
    ENCARGADOS DE PRÁCTICAS PROFESIONALES
</h4>

<div class="container mt-4">

  {{-- ALERTA FLOTANTE --}}
  @if(session('success'))
  <div id="alerta-flotante"
       class="alert alert-success shadow-lg position-fixed top-0 start-50 translate-middle-x mt-3"
       style="z-index: 1055; opacity: 0;">
       {{ session('success') }}
  </div>
  @endif

  <div class="table-responsive shadow-sm rounded" style="overflow-x: auto;">
    <table class="table table-hover align-middle table-bordered" style="min-width: 1300px;">

      <thead style="background: linear-gradient(135deg, #384daaff 0%, #84a3e2ff 100%);">
        <tr>
          <th class="text-center text-uppercase text-white fw-bold" style="background: transparent; font-size: 1.1rem; padding: 15px; white-space: nowrap;">RPE</th>
          <th class="text-center text-uppercase text-white fw-bold" style="background: transparent; font-size: 1.1rem; padding: 15px; white-space: nowrap;">Nombre</th>
          <th class="text-center text-uppercase text-white fw-bold" style="background: transparent; font-size: 1.1rem; padding: 15px; white-space: nowrap;">Reportes por Alumno</th>
          <th class="text-center text-uppercase text-white fw-bold" style="background: transparent; font-size: 1.1rem; padding: 15px; white-space: nowrap;">Carreras Asignadas</th>
          <th class="text-center text-uppercase text-white fw-bold" style="background: transparent; font-size: 1.1rem; padding: 15px; white-space: nowrap;">Solicitudes Asignadas</th>
          <th class="text-center text-uppercase text-white fw-bold" style="background: transparent; font-size: 1.1rem; padding: 15px; white-space: nowrap;">Reportes Asignados</th>
          <th class="text-center text-uppercase text-white fw-bold" style="background: transparent; font-size: 1.1rem; padding: 15px; white-space: nowrap;">Evaluaciones Asignados</th>
          <th class="text-center text-uppercase text-white fw-bold" style="background: transparent; font-size: 1.1rem; padding: 15px; white-space: nowrap;">Acciones</th>
        </tr>
      </thead>

      <tbody>
        @foreach ($encargados as $encargado)
        <tr>
            {{-- Solo RPE en negritas --}}
            <td class="text-center fw-bold">{{ $encargado->RPE }}</td>

            <td class="text-center">{{ $encargado->Nombre }}</td>

            <td class="text-center" style="color:#000;">
                {{ $encargado->reportes_requeridos[0] ?? '-' }}
            </td>

            {{-- CARRERAS --}}
            <td class="text-center">
              @forelse($encargado->listaCarreras as $carrera)
                <span class="badge rounded-pill px-3 py-2 mb-2 d-block"
                      style="color:#000; font-size: 1rem; font-weight: normal;">
                  {{ $carrera }}
                </span>
              @empty
                <span class="text-muted fst-italic">Sin asignar</span>
              @endforelse
            </td>

            <td class="text-center" style="color:#000;">
                {{ $encargado->solicitudes_asignadas }}
            </td>

            <td class="text-center" style="color:#000;">
                {{ $encargado->reportes_asignados }}
            </td>

            <td class="text-center" style="color:#000;">
                {{ $encargado->evaluaciones_asignadas }}
            </td>

            <td class="text-center">
              <button class="btn btn-primary btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#editarCarreras{{ $encargado->Id_Encargado }}">
                  <i class="bi bi-pencil-square"></i> Editar
              </button>
            </td>
        </tr>

        {{-- MODAL EDITAR CARRERAS --}}
        <div class="modal fade" id="editarCarreras{{ $encargado->Id_Encargado }}" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <form action="{{ route('administrador.encargados.updateCarreras', $encargado->Id_Encargado) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- HEADER --}}
                <div class="modal-header text-white py-3" style="background: linear-gradient(90deg, #00124E, #003B95);">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-briefcase-fill me-2"></i> EDITAR CARRERAS ASIGNADAS
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body px-4">

                    <p class="text-muted mb-3 fw-bold" style="font-size: 1rem;">
                        Selecciona las carreras que estarán a cargo del encargado:
                    </p>

                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach($carreras as $c)
                        <div class="form-check d-flex align-items-center py-1">

                            <input
                                class="form-check-input me-2"
                                type="checkbox"
                                name="carreras[]"
                                value="{{ mb_strtoupper($c->Descripcion_Mayúsculas) }}"
                                id="carrera{{ $encargado->Id_Encargado }}_{{ $loop->index }}"
                                {{ in_array(mb_strtoupper($c->Descripcion_Mayúsculas), $encargado->listaCarreras ?? []) ? 'checked' : '' }}>

                            <label class="form-check-label w-100"
                                for="carrera{{ $encargado->Id_Encargado }}_{{ $loop->index }}">
                                {{ $c->Descripcion_Mayúsculas }}
                            </label>
                        </div>
                        @endforeach
                    </div>

                    {{-- NUEVO CAMPO: Número de reportes --}}
                    <p class="text-muted mb-2 fw-bold">Número de reportes requeridos por alumno:</p>

                    <input type="number"
                        name="numero_reportes"
                        class="form-control form-control-lg mb-2"
                        style="border-radius: 12px;"
                        min="1"
                        max="10"
                        value="{{ $encargado->reportes_requeridos[0] ?? 1 }}"
                        required>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-0 px-4 pb-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </button>

                    <button type="submit" class="btn text-white rounded-pill px-4"
                            style="background:#0053A9; transition: 0.2s;">
                        <i class="bi bi-check-lg me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
            </div>
          </div>
        </div>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection


@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {

  // Alerta flotante
  const alerta = document.getElementById("alerta-flotante");
  if (alerta) {
    alerta.style.transition = "opacity .6s ease";
    alerta.style.opacity = "1";
    setTimeout(() => {
      alerta.style.opacity = "0";
      alerta.addEventListener('transitionend', () => alerta.remove());
    }, 3000);
  }

  // Select2
  $('.select2').select2({
    placeholder: "Selecciona carreras...",
    allowClear: true,
    width: '100%',
    dropdownParent: $('.modal.show')
  });

});
</script>
@endsection
