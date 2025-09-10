<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(to bottom, #71859b, #e0f2fe);
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .header-logos {
      background-color: #003f84;
      display: flex;
      align-items: center;
      padding: 30px 20px;
    }

    .header-logos img {
      height: 60px;
    }

    .separator {
      width: 1px;
      height: 50px;
      background-color: white;
      margin: 0 15px;
    }

    .light-blue-line {
      height: 8px;
      background-color: #00B2E3;
    }

    .footer-text {
      font-size: 12px;
      line-height: 1.2;
      padding: 8px 0;
      margin: 0;
    }

    .footer-link {
      color: green;
      text-decoration: none;
    }

    .footer-link:hover {
      text-decoration: underline;
    }

    .label-fixed {
      width: 130px;
    }
  </style>
</head>
<body>

  <div class="card shadow border mx-auto w-100" style="max-width: 750px;">

    <!-- Header con logos -->
    <div class="header-logos">
      <img src="{{ asset('images/uaslp-logo.png') }}" alt="Logo UASLP">
      <div class="separator"></div>
      <img src="{{ asset('images/logo-facultad-ingenieria.png') }}" alt="Logo FI">
    </div>

    <!-- Línea azul claro -->
    <div class="light-blue-line"></div>

    <!-- Formulario -->
    <div class="p-4">
      <h6 class="text-center mb-4">SISTEMA DE CONTROL DE PRÁCTICAS PROFESIONALES</h6>

      <form class="mx-auto" style="max-width: 500px;">
        <div class="row mb-3 align-items-center">
          <label for="cuenta" class="col-form-label label-fixed text-start">Cuenta UASLP</label>
          <div class="col">
            <input type="text" class="form-control" id="cuenta" name="cuenta" placeholder="Correo UASLP / RPE / 'A' + Clave única" required>
          </div>
        </div>

        <div class="row mb-3 align-items-center">
            <label for="password" class="col-form-label label-fixed text-start">Contraseña</label>
            <div class="col">
                <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100" style="background-color: #005ecb;">Ingresar</button>
      </form>
    </div>

    <!-- Footer -->
    <div class="text-center text-muted footer-text">
      Facultad de Ingeniería, UASLP<br>
      Dr. Manuel Nava #8, Zona Universitaria poniente<br>
      Tels: (444) 826.2330 al 2339<br>
      <a href="http://www.ingenieria.uaslp.mx" class="footer-link">http://www.ingenieria.uaslp.mx</a><br><br>
    </div>
  </div>

</body>
</html>
