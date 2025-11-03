@extends('layouts.administrador')
@section('title', 'Administración de Roles')

@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary mb-0">Panel de Administración de Roles</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#agregarEmpleadoModal">
      <i class="bi bi-person-plus"></i> Agregar Empleado
    </button>
  </div>

  {{-- ✅ Alerta flotante --}}
  @if(session('success'))
    <div id="alerta-flotante" 
         class="alert alert-success shadow position-fixed top-0 start-50 translate-middle-x mt-3"
         style="z-index: 1055; opacity: 0; width: auto; max-width: 600px; text-align: center;">
      {{ session('success') }}
    </div>
  @endif

  <table class="table table-bordered text-center align-middle shadow-sm">
    <thead class="table-dark">
      <tr>
        <th>RPE</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Cargo</th>
        <th>Área</th>
        <th>Carrera</th>
        <th>Rol Actual</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($empleados as $empleado)
      <tr>
        <td>{{ $empleado->RPE }}</td>
        <td>{{ $empleado->Nombre }}</td>
        <td>{{ $empleado->Correo ?? 'No registrado' }}</td>
        <td>{{ $empleado->Cargo ?? '—' }}</td>
        <td>{{ $empleado->Area ?? '—' }}</td>
        <td>{{ $empleado->Carrera ?? '—' }}</td>
        <td>
          <span class="badge bg-primary">
            {{ $empleado->rol->nombre ?? 'Sin rol' }}
          </span>
        </td>
        <td>
          {{-- 🔸 Botón editar --}}
          <button class="btn btn-sm btn-warning"
                  data-bs-toggle="modal"
                  data-bs-target="#editarEmpleadoModal{{ $empleado->Id_Encargado }}">
            <i class="bi bi-pencil-square"></i> Editar
          </button>

          {{-- 🔸 Botón eliminar --}}
          <button class="btn btn-sm btn-danger"
                  data-bs-toggle="modal"
                  data-bs-target="#confirmarEliminarModal{{ $empleado->Id_Encargado }}">
            <i class="bi bi-trash"></i> Eliminar
          </button>

          {{-- 🔸 Modal confirmar eliminación --}}
          <div class="modal fade" id="confirmarEliminarModal{{ $empleado->Id_Encargado }}" tabindex="-1" aria-labelledby="confirmarEliminarLabel{{ $empleado->Id_Encargado }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                  <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar eliminación
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body text-center">
                  <p>¿Seguro que deseas eliminar a <strong>{{ $empleado->Nombre }}</strong>?</p>
                  <p class="mb-1"><strong>RPE:</strong> {{ $empleado->RPE }}</p>
                  <p><strong>Área:</strong> {{ $empleado->Area ?? '—' }}</p>
                </div>

                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  <form action="{{ route('administrador.empleados.destroy', $empleado->Id_Encargado) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
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

{{-- 🔹 Modal agregar empleado --}}
<div class="modal fade" id="agregarEmpleadoModal" tabindex="-1" aria-labelledby="agregarEmpleadoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('administrador.empleados.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="agregarEmpleadoLabel">
            <i class="bi bi-person-plus"></i> Registrar Nuevo Empleado
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">RPE</label>
              <input type="text" name="RPE" class="form-control" required>
            </div>
            <div class="col-md-8">
              <label class="form-label fw-semibold">Nombre</label>
              <input type="text" name="Nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Área</label>
              <input type="text" name="Area" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Carrera</label>
              <input type="text" name="Carrera" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Cargo</label>
              <input type="text" name="Cargo" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Correo Electrónico</label>
              <input type="email" name="Correo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Teléfono</label>
              <input type="text" name="Telefono" class="form-control">
            </div>
            <div class="col-md-6">
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- 🔹 Modal editar empleado --}}
@foreach ($empleados as $empleado)
<div class="modal fade" id="editarEmpleadoModal{{ $empleado->Id_Encargado }}" tabindex="-1" aria-labelledby="editarEmpleadoLabel{{ $empleado->Id_Encargado }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('administrador.empleados.update', $empleado->Id_Encargado) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Editar datos de {{ $empleado->Nombre }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
              <label class="form-label fw-semibold">Correo Electrónico</label>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
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
    alerta.style.transition = "opacity 0.5s ease";
    alerta.style.opacity = "1";
    setTimeout(() => {
      alerta.style.opacity = "0";
      alerta.addEventListener('transitionend', () => alerta.remove());
    }, 3000);
  }
});
</script>
@endsection
