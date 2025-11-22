@extends('layouts.dsspp')
@section('title', 'Cartas de Presentación – DSSPP')

@push('styles')
<style>
    .card-lista {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 1rem;
        transition: 0.3s;
    }

    .card-lista:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .pendiente { background:#fff3cd; color:#856404; }
    .proceso { background:#cce5ff; color:#004085; }
    .realizado { background:#d4edda; color:#155724; }
    .rechazado { background:#f8d7da; color:#721c24; }
</style>
@endpush


@section('content')
<div class="container-fluid my-0 p-0">

    <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
        CARTAS DE PRESENTACIÓN – DSSPP
    </h4>

    <div class="p-4">

        {{-- FILTROS --}}
        <div class="filter-section mb-4 p-3 bg-white rounded shadow-sm">

            <div class="row g-3">

                {{-- BUSCADOR --}}
                <div class="col-md-6">
                    <input type="text" id="buscar" class="form-control" placeholder="Buscar por nombre o clave...">
                </div>

                {{-- FILTRO ESTADO --}}
                <div class="col-md-3">
                    <select id="filtroEstado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="proceso">Proceso</option>
                        <option value="realizado">Realizado (Aprobado)</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>

                {{-- FILTRO CARRERA --}}
                <div class="col-md-3">
                    <select id="filtroCarrera" class="form-select">
                        <option value="">Todas las carreras</option>
                        @foreach(\App\Models\CarreraIngenieria::all() as $c)
                            <option value="{{ $c->Descripcion_Mayúsculas }}">{{ $c->Descripcion_Mayúsculas }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        {{-- LISTA --}}
        <table class="table table-bordered text-center align-middle" id="tablaCartas">
            <thead class="table-dark">
                <tr>
                    <th>Clave</th>
                    <th>Alumno</th>
                    <th>Carrera</th>
                    <th>Estado</th>
                    <th>Carta</th>
                </tr>
            </thead>

            <tbody>

            @foreach($alumnos as $a)

                @php 
                    $alumno = \App\Models\Alumno::find($a->clave_alumno);

                    $sol = \App\Models\SolicitudFPP01::where('Clave_Alumno', $a->clave_alumno)
                        ->where('Autorizacion', 1)
                        ->first();

                    $exp = $sol
                        ? \App\Models\Expediente::where('Id_Solicitud_FPP01', $sol->Id_Solicitud_FPP01)->first()
                        : null;

                    $tieneCarta = $exp && $exp->Carta_Presentacion;
                    $estado = strtolower($a->estado);
                @endphp

                <tr class="filaCarta"
                    data-busqueda="{{ strtolower($alumno->Nombre . ' ' . $alumno->ApellidoP_Alumno . ' ' . $alumno->Clave_Alumno) }}"
                    data-estado="{{ $estado }}"
                    data-carrera="{{ strtolower($alumno->Carrera) }}"
                >

                    <td>{{ $alumno->Clave_Alumno }}</td>

                    <td>{{ $alumno->Nombre }} {{ $alumno->ApellidoP_Alumno }}</td>

                    <td>{{ $alumno->Carrera }}</td>

                    <td>
                        <span class="status-badge {{ $estado }}">
                            {{ ucfirst($estado) }}
                        </span>
                    </td>

                    <td>
                        @if($tieneCarta)
                            <a class="btn btn-success btn-sm" target="_blank"
                            href="{{ route('dsspp.carta.preview', $a->clave_alumno) }}">
                                Ver PDF
                            </a>
                        @else
                            <form action="{{ route('dsspp.carta.generar', $a->clave_alumno) }}"
                                method="POST" style="display:inline">
                                @csrf
                                <button class="btn btn-primary btn-sm">
                                    Generar
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>

            @endforeach

            </tbody>
        </table>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const buscarInput = document.getElementById('buscar');
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroCarrera = document.getElementById('filtroCarrera');
    const filas = document.querySelectorAll('.filaCarta');

    // Quitar acentos y normalizar texto
    function normalizar(texto) {
        return texto.toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
    }

    function filtrar() {

        const textoBuscar = normalizar(buscarInput.value);
        const estadoSeleccionado = filtroEstado.value;
        const carreraSeleccionada = normalizar(filtroCarrera.value);

        filas.forEach(fila => {

            const textoFila = normalizar(fila.dataset.busqueda);
            const estadoFila = fila.dataset.estado;
            const carreraFila = normalizar(fila.dataset.carrera);

            const coincideTexto = textoFila.includes(textoBuscar);
            const coincideEstado = estadoSeleccionado === "" || estadoFila === estadoSeleccionado;
            const coincideCarrera = carreraSeleccionada === "" || carreraFila === carreraSeleccionada;

            if (coincideTexto && coincideEstado && coincideCarrera) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        });
    }

    buscarInput.addEventListener("input", filtrar);
    filtroEstado.addEventListener("change", filtrar);
    filtroCarrera.addEventListener("change", filtrar);
});
</script>
@endpush