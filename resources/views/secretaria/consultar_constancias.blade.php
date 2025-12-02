@extends('layouts.secretaria')

@section('title','Carta Validación - Generada')

@push('styles')
<style>
/* TÍTULO */
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

/* TARJETA */
.main-card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* FILTROS */
.filter-box input,
.filter-box select {
    height: 45px;
    border-radius: 10px;
    font-size: .95rem;
    padding-left: 15px;
}

/* TABLA */
.custom-table th {
    background-color: #003366;
    color: white;
    padding: 14px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: .8rem;
}

.custom-table td {
    padding: 16px;
    vertical-align: middle;
}

/* BOTÓN */
.btn-generar {
    background: #0077b6;
    color: #fff;
    font-weight: 600;
    border-radius: 8px;
    padding: 6px 16px;
}

.btn-generar:hover {
    background: #005f8c;
}

/* BADGE FECHA */
.badge-fecha {
    background: #28a745;
    color: white;
    font-size: .85rem;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

</style>
@endpush


@section('content')

<div class="header-title">LISTADO DE ALUMNOS - CARTA DE VALIDACION GENERADA</div>

<div class="main-card">

    {{-- FILTROS --}}
    <div class="row mb-4 filter-box">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por clave o nombre...">
        </div>

        <div class="col-md-4">
            <select id="filtroCarrera" class="form-select">
                <option value="">Todas las carreras</option>
                @foreach($constancias->pluck('carrera')->unique() as $car)
                    <option value="{{ $car }}">{{ $car }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ALERTA --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- TABLA --}}
    <table class="table custom-table" id="tablaConstancias">
        <thead>
            <tr>
                <th>Clave</th>
                <th>Nombre</th>
                <th>Carrera</th>
                <th>Fecha Término</th>
                <th class="text-center">Acción</th>
            </tr>
        </thead>

        <tbody>
            @forelse($constancias as $al)
            <tr>
                <td>{{ $al->clave }}</td>
                <td>{{ $al->nombre }}</td>
                <td>{{ $al->carrera }}</td>

                <td>
                    <span class="badge-fecha">
                        {{ $al->fecha ? \Carbon\Carbon::parse($al->fecha)->format('Y-m-d') : '---' }}
                    </span>
                </td>

                <td class="text-center">
                    <a href="{{ route('secretaria.constancia.ver', $al->clave) }}" 
                    class="btn btn-success">
                        <i class="bi bi-file-earmark-pdf"></i> Ver Constancia
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay alumnos pendientes.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


{{-- MODAL --}}
<div class="modal fade" id="modalGenerar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-primary">

          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">Confirmar Generación</h5>
              <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
              <p class="fw-bold">¿Deseas generar la constancia del alumno?</p>
              <p>
                <strong>Clave:</strong> <span id="gc_clave"></span><br>
                <strong>Alumno:</strong> <span id="gc_nombre"></span>
              </p>
          </div>

          <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

              <form id="formGenerar" method="POST">
                @csrf
                <button class="btn btn-primary">Sí, generar</button>
              </form>
          </div>

      </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    // Modal dinámico
    var modal = document.getElementById('modalGenerar');
    modal.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget;
        document.getElementById('gc_clave').textContent = button.dataset.clave;
        document.getElementById('gc_nombre').textContent = button.dataset.nombre;

        document.getElementById('formGenerar').action =
            "/secretaria/constancias/generar/" + button.dataset.clave;
    });

    // FILTRO BUSCADOR
    const searchInput = document.getElementById('searchInput');
    const tabla = document.getElementById('tablaConstancias').getElementsByTagName('tbody')[0];

    searchInput.addEventListener('keyup', function () {
        let filtro = this.value.toLowerCase();
        for (let row of tabla.rows) {
            let texto = row.innerText.toLowerCase();
            row.style.display = texto.includes(filtro) ? '' : 'none';
        }
    });

    // FILTRO CARRERA
    const filtroCarrera = document.getElementById('filtroCarrera');

    filtroCarrera.addEventListener('change', function () {
        let carrera = this.value.toLowerCase();
        for (let row of tabla.rows) {
            let car = row.cells[2].innerText.toLowerCase();
            row.style.display = carrera === '' || car === carrera ? '' : 'none';
        }
    });

});
</script>
