@extends('layouts.alumno')

@section('title','REPORTES PARCIALES')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">

@section('content')

<div class="container-fluid my-0 p-0">
    <!-- Header -->
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-file-earmark-text me-2"></i>
                DETALLE COMPLETO DE SOLICITUD FPP01
            </h4>
        </div>
    </div>
</div>
@endsection
