<!DOCTYPE html>
<html>
<head>
  <style>
    .encabezado {
      width: 100%;
      display: table;
      table-layout: fixed;
      margin-top: 30px;
      margin-bottom: 20px;
    }

    .columnaEncabezado {
      display: table-cell;
      vertical-align: middle;
      padding: 5px;
    }

    .centro {
      width: 76%;
    }

    .izquierdaEncabezado, .derechaEncabezado {
      width: 12%;
    }

    .datosAlumno {
      display: table;
      width: 100%;
      table-layout: fixed;
      font-size: 13px;
      margin-bottom: 20px;
    }

    .columnaDatos {
      display: table-cell;
      vertical-align: top;
    }

    .izquierdaDatos {
      width: 20%;
    }

    .derechaDatos {
      width: 80%;
    }

    img {
      max-width: 100%;
      height: auto;
    }

    .tabla {
      font-size: 13px;
      width: 100%;
      text-align: center;
      border-collapse: collapse;
      border: 1px solid #000;
      margin-bottom: 60px;
    }

    td {
      border: 1px solid black;
      padding: 6px;
    }

    .datosInstitucion {
      font-size: 14px;
      padding: 15px;
    }
  </style>
</head>
<body>
  <div class="encabezado">
    <div class="columnaEncabezado izquierdaEncabezado">
      <img src="{{ public_path('images/logoAutonoma.png') }}" alt="Izquierda">
    </div>
    <div class="columnaEncabezado centro" style="text-align: center">
      <div style="text-align: center; font-size: 15px; font-weight: bold">
        UNIVERSIDAD AUTÓNOMA DE SAN LUIS POTOSÍ
      </div>
      <div style="text-align: center; font-size: 13px; font-weight: bold; margin-bottom: 7px">
        FACULTAD DE INGENIERÍA
      </div>
      <div style="text-align: center; font-size: 15px; font-weight: bold">
        CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES
      </div>
      <div style="text-align: center; font-size: 15px">
        PRÁCTICAS PROFESIONALES
      </div>
    </div>
    <div class="columnaEncabezado derechaEncabezado">
      <img src="{{ public_path('images/logoIngenieria.png') }}" alt="Derecha">
    </div>
  </div>
  <div style="text-align: right; margin-right: 40px">
      {{ $fechaHoy }}
  </div>
  <div class="contenido" style="font-weight: bold; margin-left: 20px; margin-right: 20px">
      <div class="datosAlumno">
        <div class="columnaDatos izquierdaDatos">
          <div>NOMBRE:</div>
          <div>CLAVE:</div>
          <div>CARRERA:</div>
        </div>
        <div class="columnaDatos derechaDatos">
          <div>{{ $alumno['Nombre'] ?? 'S/D' }} {{ $alumno['ApellidoP_Alumno'] ?? 'S/D' }} {{ $alumno['ApellidoM_Alumno'] ?? 'S/D' }}</div>
          <div>{{ $alumno['Clave_Alumno'] ?? 'S/D' }}</div>
          <div>{{ $alumno['Carrera'] ?? 'S/D' }}</div>
        </div>
      </div>
      <table class="tabla">
        <tr style="background-color: #E0EBFF">
          <td>Proyecto Prácticas Profesionales</td>
          <td>No°</td>
          <td>Materias Validadas en la Facultad de Ingenieria</td>
          <td>Nivel</td>
          <td>Cred</td>
          <td>Cal</td>
        </tr>
        <tr>
          <td colspan="6">2024-2025/II</td>
        </tr>
        <tr>
          <td>{{ $solicitud['Nombre_Proyecto'] ?? 'S/D' }}</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td>Acreditada</td>
        </tr>
      </table>
      <div class="datosInstitucion">
        <div>Prácticas que se validan: </div>
        <div>Materias que se validan en la Facultad de Ingeniería: </div>
        <div>Total de créditos validados: </div>
        <div style="margin-bottom: 50px">Nota: Esta constancia de validación se extiende de común 
        acuerdo con la Jefatura del Área de {{ $area['Area'] ?? 'S/D' }} y la coordinación 
        de la carrera {{ $alumno['Carrera'] ?? 'S/D' }}.</div>
        <div style="text-align: center">
          <div style="margin-bottom: 100px">"MODOS ET CUNCTARUM RERUM MENSURAS AUDEBO"</div>
          <div>Dr. Francisco Gerardo Pérez Gutiérrez</div>
          <div style="margin-bottom: 60px">Secretario Académico de la Facultad de Ingeniería</div>
        </div>
        <div style="text-align: left; font-weight: normal; font-size: 11px">c.c.p 'Area de {{ $area['Area'] ?? 'S/D' }}'</div>
        <div style="text-align: left; font-weight: normal; font-size: 11px">c.c.p. Departamento de Servicio Social y Prácticas Profesionales</div>
      </div>
  </div>
</body>
</html>
