@extends('layouts.dsspp')
@section('title','Consultar alumno por clave')



@push('styles')
<style>
  .search-container {
    max-width: 800px;
    margin: 0 auto;
  }
  
  .search-box {
    position: relative;
  }
  
  .search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
  }
  
  .form-control-search {
    padding-left: 45px;
    height: 50px;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    font-size: 1rem;
  }
  
  .form-control-search:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
  }
</style>
@endpush

@section('content')
<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    CONSULTAR ALUMNO
  </h4>
  
  <div class="bg-white p-4 rounded shadow-sm">
    
    {{-- Buscador --}}
    <div class="search-container mb-4">
      <div class="search-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Busca por:</strong> Clave única
      </div>
      
      <form action="{{ route('encargado.consultar_alumno') }}" method="GET">
        <div class="search-box">
          <i class="bi bi-search search-icon"></i>
          <input 
            type="text" 
            name="busqueda" 
            class="form-control form-control-search" 
            value="{{ request('busqueda') }}"
          >
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-3 py-2">
          <i class="bi bi-search me-2"></i> Buscar Alumno
        </button>
      </form>
    </div>
  </div>
@endsection

