@extends('layouts.administrador') {{-- O tu layout principal --}}
@section('title', 'Bitácora del Sistema')

@section('content')
<style>
.page-title {
    background:#003366;
    color:white;
    padding:15px;
    font-weight:bold;
    text-align:center;
    font-size:20px;
}
.table-bitacora thead {
    background:#005eb8;
    color:white;
}
.filter-box {
    background:#e9eef7;
    padding:10px;
    border-radius:8px;
}
label {
    font-weight:600;
}
</style>

<div class="page-title">
    BITÁCORA DEL SISTEMA
</div>

<div class="container mt-4">

    {{-- ✅ Filtros --}}
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

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Buscar</button>
            </div>

        </div>
    </form>

    {{-- ✅ Tabla --}}
    <div class="table-responsive">
        <table class="table table-bordered table-bitacora">
            <thead class="text-center">
                <tr>
                    <th>Movimiento</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>ID</th>
                    <th>Nombre</th>
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
    </div>
</div>
@endsection
