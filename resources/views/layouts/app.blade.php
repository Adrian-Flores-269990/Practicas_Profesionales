<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sistema de Prácticas Profesionales</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background: #f0f4f8;
    }

    header {
      background-color: #004795;
      padding: 1rem;
    }

    .header-content {
      display: flex;
      gap: 1rem;
    }

    .header-content img {
      height: 60px;
    }

    .container.header-content {
    padding-left: 0;
    padding-right: 0;
    margin-left: 0;
    }

    .header-separator {
      width: 1.5px;
      height: 55px;
      background-color: white;
    }

    .footer {
        background-color: #004795;
        color: white;
        text-align: center;
        padding: 0.5rem 0;
        font-size: 0.75rem;
        line-height: 1.1;
    }

    .footer p {
        margin-bottom: 0.25rem;
    }

    .footer a {
        color: #16a34a;
        text-decoration: none;
    }

    .footer a:hover {
        text-decoration: underline;
    }

    .light-blue-line {
      height: 10px;
      background-color: #00B2E3;
      width: 100%;
    }

    main {
      flex: 1;
      background-color: white;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="text-white">
  <div class="container-fluid header-content">
    <img src="{{ asset('images/uaslp-logo.png') }}" alt="Logo UASLP">
    <div class="header-separator"></div>
    <img src="{{ asset('images/logo-facultad-ingenieria.png') }}" alt="Logo FI">
  </div>
</header>

  <div class="light-blue-line"></div>

  <!-- Contenido dinámico -->
  <main class="pt-0 pb-4">
    @yield('content')
  </main>

  <div class="light-blue-line"></div>

  <!-- Footer -->
  <footer class="footer">
    <p class="mb-1">Facultad de Ingeniería, UASLP</p>
    <p class="mb-1">Dr. Manuel Nava #8, Zona Universitaria poniente</p>
    <p class="mb-1">Tel. (444) 826.2330 al 2339</p>
    <a href="http://www.ingenieria.uaslp.mx" target="_blank">http://www.ingenieria.uaslp.mx</a>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
