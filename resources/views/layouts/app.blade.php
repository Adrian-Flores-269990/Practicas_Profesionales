<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Sistema de Prácticas Profesionales')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  @stack('styles')
</head>
<body>

  @include('partials.header')
  <div class="light-blue-line"></div>

  {{-- submenú por rol (admin/alumno) --}}
  @yield('subheader')

  <main class="pt-0 pb-4 container-fluid">
    @yield('content')
  </main>

  <div class="light-blue-line"></div>
{{-- Footer (permite override por vista) --}}
@hasSection('footer_custom')
  @yield('footer_custom')    {{-- la vista puede poner su propio footer --}}
@else
  @include('partials.footer') {{-- footer global por defecto --}}
@endif

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  @stack('scripts')
</body>
</html>
