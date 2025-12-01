@extends('layouts.encargado')

@section('title', 'Calificación Final')

@section('content')
<div class="container mt-4">

    <h3 class="text-center mb-4 bg-primary text-white p-3 rounded">
        <i class="bi bi-person-check me-2"></i> ALUMNOS EN CALIFICACIÓN FINAL
    </h3>

    @if($alumnos->isEmpty())
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>
            No hay alumnos pendientes de calificación final.
        </div>
    @else

    <table class="table table-bordered table-hover shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>Alumno</th>
                <th>Carrera</th>
                <th>Reportes</th>
                <th>Promedio</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($alumnos as $a)
            <tr>
                <td>{{ $a->NombreAlumno }}</td>
                <td>{{ $a->Carrera }}</td>
                <td>{{ $a->totalReportes }}</td>
                <td><span class="badge bg-primary">{{ $a->promedio }}</span></td>
                <td>
                    <a href="{{ route('encargado.calificacion.ver', $a->Id_Solicitud_FPP01) }}"
                       class="btn btn-sm btn-info">
                        Revisar
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>

    @endif
</div>
@endsection
