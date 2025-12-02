@extends('layouts.encargado')

@section('title','Liberación de Alumnos')

@push('styles')
<style>
    /* CABECERA */
    .header-title {
        background: #000066;
        color: white;
        padding: 18px;
        font-size: 1.4rem;
        font-weight: 700;
        text-align: center;
        border-radius: 4px;
        margin-bottom: 25px;
    }

    /* TARJETA PRINCIPAL */
    .main-card {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 40px;
    }

    /* BUSCADOR */
    .search-box {
        position: relative;
    }
    .search-box input {
        padding-left: 40px;
        height: 48px;
        border-radius: 10px;
    }
    .search-icon {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: #6c757d;
    }

    /* TABLA */
    .custom-table th {
        background-color: #dbe8ff;
        color: #001f4d;
        border: none;
        font-weight: 700;
        text-transform: uppercase;
        font-size: .8rem;
        padding: 14px;
    }

    .custom-table td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
    }

    /* AVATAR LETRA */
    .avatar-circle {
        width: 38px;
        height: 38px;
        background: #0066cc;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        border-radius: 50%;
    }

    /* BADGE ESTADO */
    .estado-pendiente {
        background: #fff3cd;
        border-radius: 15px;
        padding: 6px 14px;
        color: #856404;
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* BOTONES */
    .btn-aprobar {
        background: #28a745;
        color: white;
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 600;
    }
    .btn-aprobar:hover {
        background: #218838;
        color: white;
    }

    .btn-rechazar {
        background: #dc3545;
        color: white;
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 600;
    }
    .btn-rechazar:hover {
        background: #b52a36;
        color: white;
    }
</style>
@endpush

@section('content')

<div class="header-title">LIBERACIÓN DE ALUMNOS</div>

<div class="main-card">

    {{-- BUSCADOR --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" 
                       id="searchInput"
                       class="form-control"
                       placeholder="Buscar por clave o nombre del alumno...">
            </div>
        </div>
    </div>

    {{-- TABLA --}}
    <table class="table custom-table" id="tablaLiberacion">
        <thead>
            <tr>
                <th>Clave</th>
                <th>Nombre Completo</th>
                <th>Carrera</th>
                <th>Estado</th>
                <th class="text-center">Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alumnos as $al)
            <tr>
                <td class="fw-bold text-primary">{{ $al->clave }}</td>

                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2">
                            {{ strtoupper(substr($al->nombre, 0, 1)) }}
                        </div>
                        <div>
                            <strong>{{ $al->nombre }}</strong>
                        </div>
                    </div>
                </td>

                <td>
                    <i class="bi bi-mortarboard-fill text-primary me-1"></i>
                    {{ $al->carrera }}
                </td>

                <td>
                    <span class="estado-pendiente">
                        <i class="bi bi-clock-history me-1"></i> Pendiente
                    </span>
                </td>

                <td class="text-center">

                    <!-- Botón Aprobar -->
                    <button class="btn btn-aprobar btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalAprobar"
                            data-clave="{{ $al->clave }}"
                            data-nombre="{{ $al->nombre }}">
                        Aprobar
                    </button>

                    <!-- Botón Rechazar -->
                    <button class="btn btn-rechazar btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalRechazar"
                            data-clave="{{ $al->clave }}"
                            data-nombre="{{ $al->nombre }}">
                        Rechazar
                    </button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

@endsection


<!-- ================================
 MODAL CONFIRMAR APROBAR
================================ -->
<div class="modal fade" id="modalAprobar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-success">
          
          <div class="modal-header bg-success text-white">
              <h5 class="modal-title">Confirmar Aprobación</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
              <p class="fw-bold">¿Deseas aprobar la liberación del alumno?</p>
              <p>
                <strong>Clave:</strong> <span id="ap_clave"></span><br>
                <strong>Alumno:</strong> <span id="ap_nombre"></span>
              </p>
          </div>

          <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

              <form id="formAprobar" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-success">Sí, aprobar</button>
              </form>
          </div>

      </div>
  </div>
</div>


<!-- ================================
 MODAL CONFIRMAR RECHAZAR
================================ -->
<div class="modal fade" id="modalRechazar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-danger">

          <div class="modal-header bg-danger text-white">
              <h5 class="modal-title">Confirmar Rechazo</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
              <p class="fw-bold">¿Deseas rechazar la liberación del alumno?</p>
              <p>
                <strong>Clave:</strong> <span id="re_clave"></span><br>
                <strong>Alumno:</strong> <span id="re_nombre"></span>
              </p>
          </div>

          <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

              <form id="formRechazar" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-danger">Sí, rechazar</button>
              </form>
          </div>

      </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // ========== FILTRO DE BUSQUEDA ==========
    const searchInput = document.getElementById("searchInput");
    const tableRows = document.querySelectorAll("#tablaLiberacion tbody tr");

    searchInput.addEventListener("input", function () {
        let term = this.value.toLowerCase();

        tableRows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? "" : "none";
        });
    });

    // ========== MODAL APROBAR ==========
    var modalAprobar = document.getElementById('modalAprobar');
    modalAprobar.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget;
        let clave = button.getAttribute('data-clave');
        let nombre = button.getAttribute('data-nombre');

        document.getElementById('ap_clave').textContent = clave;
        document.getElementById('ap_nombre').textContent = nombre;

        document.getElementById('formAprobar').action = "/encargado/liberacion-aprobar/" + clave;
    });

    // ========== MODAL RECHAZAR ==========
    var modalRechazar = document.getElementById('modalRechazar');
    modalRechazar.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget;
        let clave = button.getAttribute('data-clave');
        let nombre = button.getAttribute('data-nombre');

        document.getElementById('re_clave').textContent = clave;
        document.getElementById('re_nombre').textContent = nombre;

        document.getElementById('formRechazar').action = "/encargado/liberacion-rechazar/" + clave;
    });

});
</script>
