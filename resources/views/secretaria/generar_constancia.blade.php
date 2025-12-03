@extends('layouts.secretaria')

@section('title','Generar Constancias')

@push('styles')
<style>
    /* CABECERA */
    .header-title {
        background: #000066;
        color: white;
        padding: 16px;
        text-align: center;
        font-size: 1.3rem;
        font-weight: 700;
        border-radius: 6px;
        margin-bottom: 25px;
    }

    /* TARJETA */
    .main-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    /* BUSCADOR */
    .search-box {
        position: relative;
    }
    .search-box input {
        padding-left: 40px;
        height: 48px;
        border-radius: 10px;
        border: 2px solid #d0d4ff;
    }
    .search-box input:focus {
        border-color: #000066;
        box-shadow: 0 0 0 0.15rem rgba(0,0,102,.25);
    }
    .search-icon {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: #555;
    }

    /* SELECT */
    .form-select {
        border-radius: 10px;
        border: 2px solid #d0d4ff;
        height: 48px;
    }
    .form-select:focus {
        border-color: #000066;
        box-shadow: 0 0 0 0.15rem rgba(0, 0, 102, .25);
    }

    /* TABLA */
    .custom-table th {
        background-color: #e8ecff;
        color: #000066;
        border: none;
        padding: 12px;
        font-size: .8rem;
        text-transform: uppercase;
        font-weight: 700;
    }
    .custom-table td {
        padding: 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f1f1;
    }

    /* BOTÓN */
    .btn-generar {
        background: #0066cc;
        color: white;
        padding: 6px 16px;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-generar:hover {
        background: #004c99;
    }
</style>
@endpush

@section('content')

<div class="header-title">CONSTANCIAS PENDIENTES DE GENERAR</div>

<div class="main-card">

    {{-- ALERTA --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- FILTROS --}}
    <div class="row mb-4">

        {{-- BUSCADOR --}}
        <div class="col-md-6 mb-3">
            <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar por clave o nombre...">
            </div>
        </div>

        {{-- FILTRO CARRERA --}}
        <div class="col-md-6 mb-3">
            <select id="filtroCarrera" class="form-select">
                <option value="">Todas las carreras</option>

                @foreach($constancias->unique('carrera') as $c)
                    <option value="{{ $c->carrera }}">{{ $c->carrera }}</option>
                @endforeach
            </select>
        </div>

    </div>


    {{-- TABLA --}}
    <table class="table custom-table" id="tablaConstancias">
        <thead>
            <tr>
                <th>Clave</th>
                <th>Nombre</th>
                <th>Carrera</th>
                <th>Fecha Liberación</th>
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
                    @if($al->fecha_liberacion)
                        {{ \Carbon\Carbon::parse($al->fecha_liberacion)->format('d/m/Y') }}
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </td>

                <td class="text-center">
                    <button class="btn btn-generar"
                        data-bs-toggle="modal"
                        data-bs-target="#modalGenerar"
                        data-clave="{{ $al->clave }}"
                        data-nombre="{{ $al->nombre }}">
                        Generar Constancia
                    </button>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No hay alumnos pendientes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection


{{-- MODAL GENERAR --}}
<div class="modal fade" id="modalGenerar" tabindex="-1">
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
                    <input type="hidden" name="clave" id="gc_clave_input">
                    <button type="submit" class="btn btn-generar btn-sm">Sí, generar</button>
                </form>

            </div>

        </div>
    </div>
</div>


{{-- JS PARA FILTROS --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    // Modal
    document.getElementById('modalGenerar')
        .addEventListener('show.bs.modal', function (event) {
            let btn = event.relatedTarget;
            let clave = btn.getAttribute('data-clave');
            let nombre = btn.getAttribute('data-nombre');

            document.getElementById('gc_clave').textContent = clave;
            document.getElementById('gc_nombre').textContent = nombre;
            document.getElementById('formGenerar').action = "/secretaria/generar-constancia/" + clave;
        });

    // Filtros
    let searchInput = document.getElementById('searchInput');
    let filtroCarrera = document.getElementById('filtroCarrera');
    let tabla = document.getElementById('tablaConstancias').getElementsByTagName('tbody')[0];

    function filtrar() {
        let texto = searchInput.value.toLowerCase();
        let carrera = filtroCarrera.value.toLowerCase();

        for (let row of tabla.rows) {
            let clave = row.cells[0].innerText.toLowerCase();
            let nombre = row.cells[1].innerText.toLowerCase();
            let car = row.cells[2].innerText.toLowerCase();

            let matchTexto = clave.includes(texto) || nombre.includes(texto);
            let matchCarrera = carrera === "" || car === carrera;

            row.style.display = (matchTexto && matchCarrera) ? "" : "none";
        }
    }

    searchInput.addEventListener('keyup', filtrar);
    filtroCarrera.addEventListener('change', filtrar);

    // FORMULARIO: ENVIAR Y DESCARGAR PDF
    document.getElementById('formGenerar').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const action = form.action;

        fetch(action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {

            if (data.success) {
                // ABRIR EL PDF
                window.open(data.url, '_blank');

                // Recargar tabla
                setTimeout(() => location.reload(), 1000);

            } else {
                alert(data.message || 'Error generando constancia');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error inesperado generando el archivo.');
        });
    });

});
</script>
