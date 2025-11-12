@extends('layouts.administrador')
@section('title', 'Administración de Roles')

@section('content')

{{-- TÍTULO SUPERIOR --}}
<h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    ADMINISTRACIÓN DE ROLES
</h4>

<div class="container mt-4">
  {{-- ALERTA FLOTANTE --}}
  @if(session('success'))
    <div id="alerta-flotante"
         class="alert alert-success shadow position-fixed top-0 start-50 translate-middle-x mt-3"
         style="z-index: 1055; opacity: 0; width: auto; max-width: 600px; text-align: center;">
      {{ session('success') }}
    </div>
  @endif

  {{-- TABLA --}}
  <div class="table-responsive shadow-sm rounded">
    <table class="table table-hover align-middle table-bordered">

      {{-- ENCABEZADOS CON GRADIENTE --}}
      <thead style="background: linear-gradient(135deg, #384daaff 0%, #84a3e2ff 100%);">
        <tr>
          @php $ths = ['RPE','Nombre','Correo','Cargo','Área','Carrera','Rol Actual','Acciones']; @endphp
          @foreach($ths as $th)
            <th class="text-center text-uppercase text-white fw-bold"
                style="background: transparent; font-size: 1.1rem; padding: 15px;">
              {{ $th }}
            </th>
          @endforeach
        </tr>
      </thead>

      <tbody>
        @foreach ($empleados as $empleado)
        <tr>

          <td class="text-center fw-bold">{{ $empleado->RPE }}</td>
          <td class="text-center">{{ $empleado->Nombre }}</td>
          <td class="text-center">{{ $empleado->Correo ?? 'No registrado' }}</td>
          <td class="text-center">{{ $empleado->Cargo ?? '—' }}</td>
          <td class="text-center">
            @php
                $areas = $empleado->Area ? array_unique(array_map('trim', explode(',', $empleado->Area))) : [];
            @endphp
            {!! $areas ? implode('<br>', array_map('e', $areas)) : '—' !!}
          </td>
          <td class="text-center">{!! $empleado->Carrera ? str_replace(',', '<br>', e($empleado->Carrera)) : '—' !!}</td>

          {{-- BADGE ROL --}}
          <td class="text-center">
            <span class="badge rounded-pill px-3 py-2"
                  style="background:#001986; font-size: 0.95rem;">
              {{ $empleado->rol->nombre ?? 'Sin rol' }}
            </span>
          </td>

          <td class="text-center">

            {{-- EDITAR --}}
            <button class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#editarEmpleadoModal{{ $empleado->Id_Encargado }}">
              <i class="bi bi-pencil-square"></i>
            </button>

            {{-- ELIMINAR --}}
            <button class="btn btn-sm btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmarEliminarModal{{ $empleado->Id_Encargado }}">
              <i class="bi bi-trash"></i>
            </button>

            {{-- MODAL CONFIRMAR ELIMINAR --}}
            <div class="modal fade" id="confirmarEliminarModal{{ $empleado->Id_Encargado }}" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>

                  <div class="modal-body text-center">
                    <p>¿Seguro que deseas eliminar a <strong>{{ $empleado->Nombre }}</strong>?</p>
                    <p class="mb-1"><strong>RPE:</strong> {{ $empleado->RPE }}</p>
                    <p><strong>Área:</strong> {{ $empleado->Area ?? '—' }}</p>
                  </div>

                  <div class="modal-footer justify-content-center">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('administrador.empleados.destroy', $empleado->Id_Encargado) }}" method="POST">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </td>

        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <br>
  <div class="d-flex justify-content-end mb-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarEmpleadoModal">
      <i class="bi bi-person-plus"></i> Agregar Empleado
    </button>
  </div>
</div>

{{-- MODAL AGREGAR EMPLEADO --}}
<div class="modal fade" id="agregarEmpleadoModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('administrador.empleados.asignarRol') }}" method="POST">
        @csrf
        <div class="modal-header text-white" style="background:#0053A9;">
          <h5 class="modal-title"><i class="bi bi-person-plus"></i> Asignar Rol a Empleado</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Seleccionar Empleado sin Rol</label>
              <select name="Id_Encargado" class="form-select" required>
                <option value="">Seleccione un empleado...</option>
                @foreach ($empleadosSinRol as $emp)
                  <option value="{{ $emp->Id_Encargado }}">
                    {{ $emp->Nombre }} — {{ $emp->Correo ?? 'Sin correo' }} (RPE: {{ $emp->RPE }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Rol</label>
              <select name="Id_Rol" class="form-select" required>
                <option value="">Seleccione un rol...</option>
                @foreach ($roles as $rol)
                  <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary">Asignar Rol</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL EDITAR EMPLEADO --}}
@foreach ($empleados as $empleado)
<div class="modal fade" id="editarEmpleadoModal{{ $empleado->Id_Encargado }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('administrador.empleados.update', $empleado->Id_Encargado) }}" method="POST">
        @csrf @method('PUT')
        <div class="modal-header text-white" style="background: linear-gradient(90deg, #00124E, #003B95);">
          <h5 class="modal-title">EDITAR EMPLEADO</h5>
          <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-md-4">
              <label class="form-label fw-semibold">RPE</label>
              <input type="text" name="RPE" value="{{ $empleado->RPE }}" class="form-control" required>
            </div>

            <div class="col-md-8">
              <label class="form-label fw-semibold">Nombre</label>
              <input type="text" name="Nombre" value="{{ $empleado->Nombre }}" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Área</label>
              <input type="text" name="Area" value="{{ $empleado->Area }}" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Carrera</label>
              <input type="text" name="Carrera" value="{{ $empleado->Carrera }}" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Cargo</label>
              <input type="text" name="Cargo" value="{{ $empleado->Cargo }}" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Correo</label>
              <input type="email" name="Correo" value="{{ $empleado->Correo }}" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Teléfono</label>
              <input type="text" name="Telefono" value="{{ $empleado->Telefono }}" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Rol</label>
              <select name="Id_Rol" class="form-select">
                @foreach ($roles as $rol)
                  <option value="{{ $rol->id }}" {{ $empleado->Id_Rol == $rol->id ? 'selected' : '' }}>
                    {{ $rol->nombre }}
                  </option>
                @endforeach
              </select>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
  const alerta = document.getElementById("alerta-flotante");
  if (alerta) {
    alerta.style.transition = "opacity .6s";
    alerta.style.opacity = "1";
    setTimeout(() => {
      alerta.style.opacity = "0";
      alerta.addEventListener("transitionend", () => alerta.remove());
    }, 3000);
  }
});
</script>
@endsection
