@extends('layouts.auth')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush


@section('title','Login')

@section('content')
  <div class="card shadow border mx-auto w-100">
    <div class="header-logos">
      <img src="{{ asset('images/uaslp-logo.png') }}" alt="Logo UASLP">
      <div class="separator"></div>
      <img src="{{ asset('images/logo-facultad-ingenieria.png') }}" alt="Logo FI">
    </div>
    <div class="light-blue-line"></div>

    <div class="p-4">
      <h6 class="text-center mb-4">SISTEMA DE CONTROL DE PRÁCTICAS PROFESIONALES</h6>

      <form method="POST" action="{{ route('login') }}" class="mx-auto" style="max-width: 500px;">
        @csrf
        <div class="row mb-3 align-items-center">
          <label for="cuenta" class="col-form-label label-fixed text-start">Cuenta UASLP</label>
          <div class="col">
            <input type="text" class="form-control" id="cuenta" name="cuenta" placeholder="Correo UASLP / 'A' + Clave única" required>
            @if($errors->has('cuenta'))
              <small class="text-danger">{{ $errors->first('cuenta') }}</small>
            @endif
          </div>
        </div>

        <div class="row mb-3 align-items-center">
          <label for="password" class="col-form-label label-fixed text-start">Contraseña</label>
          <div class="col">
            <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
            @if($errors->has('password'))
              <small class="text-danger">{{ $errors->first('password') }}</small>
            @endif
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100" style="background-color:#005ecb;">Ingresar</button>
      </form>
    </div>

    <div class="text-center text-muted footer-text">
      Facultad de Ingeniería, UASLP<br>
      Dr. Manuel Nava #8, Zona Universitaria poniente<br>
      Tels: (444) 826.2330 al 2339<br>
      <a href="http://www.ingenieria.uaslp.mx" class="footer-link">www.ingenieria.uaslp.mx</a><br><br>
    </div>
  </div>
@endsection
