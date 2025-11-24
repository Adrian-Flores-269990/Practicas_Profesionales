@extends('layouts.administrador')
@section('title', 'Bitácora del Sistema')

@section('content')
<style>
    .page-title {
        background: linear-gradient(90deg, #00124E, #003B95);
        color: white;
        padding: 15px;
        font-weight: bold;
        text-align: center;
        font-size: 20px;
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .filter-box {
        background: #e9eef7;
        padding: 10px;
        border-radius: 8px;
    }

    .table-bitacora {
        width: 100%;
        border-collapse: collapse;
    }

    .table-bitacora thead {
        background: linear-gradient(135deg, #384daaff 0%, #84a3e2ff 100%);
    }

    .table-bitacora thead th {
        background: transparent;
        color: white;
        text-align: center;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 15px;
        white-space: nowrap;
    }

    .table-bitacora th, .table-bitacora td {
        padding: 12px;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-bitacora tbody tr:hover {
        background-color: #f1f5fb;
    }
</style>

<h4 class="text-center fw-bold text-white py-3 shadow-sm" style="background: linear-gradient(90deg, #00124E, #003B95);">
    BITÁCORA DEL SISTEMA
</h4>

<div class="container mt-4">
    <form method="GET" action="{{ route('admin.bitacora') }}">
        <div class="row filter-box mb-3">
            <div class="col-md-2">
                <label>ID</label>
                <input type="text" name="id" class="form-control" value="{{ request('id') }}">
            </div>
            <div class="col-md-3">
                <label>Movimiento</label>
                <input type="text" name="movimiento" class="form-control" value="{{ request('movimiento') }}">
            </div>
            <div class="col-md-2">
                <label>Fecha Inicial</label>
                <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
            </div>
            <div class="col-md-2">
                <label>Fecha Final</label>
                <input type="date" name="fecha_final" class="form-control" value="{{ request('fecha_final') }}">
            </div>
            {{-- Esta columna ocupará todo el espacio restante --}}
            <div class="col-md d-flex align-items-end">
                <button class="btn btn-primary w-100">Buscar</button>
            </div>
        </div>
    </form>

    {{-- TABLA --}}
    <div class="table-responsive shadow-sm rounded" style="overflow-x:auto;">
        <table class="table table-hover align-middle table-bitacora table-bordered">
            <thead>
                <tr>
                    <th>MOVIMIENTO</th>
                    <th>FECHA</th>
                    <th>HORA</th>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
            @forelse($registros as $bit)
                <tr>
                    <td><strong>{{ $bit->Movimiento }}</strong></td>
                    <td>{{ $bit->Fecha }}</td>
                    <td>{{ $bit->Hora }}</td>
                    <td>{{ $bit->Clave_Usuario }}</td>
                    <td>
                        @if ($bit->alumno)
                            {{ $bit->alumno->Nombre }} {{ $bit->alumno->ApellidoP_Alumno }} {{ $bit->alumno->ApellidoM_Alumno }}
                        @elseif ($bit->empleado)
                            {{ $bit->empleado->Nombre }}
                            ({{ $bit->empleado->Cargo ?? 'Encargado' }})
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $bit->IP }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay movimientos registrados</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{-- PAGINACIÓN --}}
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                @if($registros->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">‹</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $registros->previousPageUrl() }}">‹</a></li>
                @endif

                @foreach ($registros->getUrlRange(1, $registros->lastPage()) as $page => $url)
                    <li class="page-item {{ $registros->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                @if($registros->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $registros->nextPageUrl() }}">›</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">›</span></li>
                @endif
            </ul>
        </nav>
    </div>
</div>
@endsection
