@extends('layouts.encargado')
@section('title','Estadísticas empresas')

@section('content')
    <nav class="navbar" style="background-color: #000066;">
      <div class="container-fluid justify-content-center">
        <span class="navbar-text text-white mx-auto" style="font-weight: 500;">
          Estadísticas de empresas con las que se tienen convenios para prácticas profesionales
        </span>
      </div>
    </nav>

    <div class="container mt-4">
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="empresaDropdown" class="form-label fw-bold">Nombre de la empresa</label>
          <select class="form-select" id="empresaDropdown">
            <option selected disabled>Selecciona la empresa</option>
            <option value="ABB MÉXICO S.A. DE C.V.E">ABB MÉXICO S.A. DE C.V.E</option>
        </select>
        </div>
        <div class="col-md-6">
          <label for="versionDropdown" class="form-label fw-bold">Versión</label>
          <select class="form-select" id="versionDropdown">
            <option selected disabled>Selecciona versión de cuestionario</option>
            <option value="Versión 1.1 2024-2025">Versión 1.1 2024-2025</option>
        </select>
        </div>
      </div>
    </div>
    <!-- CONTENEDOR DE GRÁFICA + PREGUNTAS LATERALES -->
    <div id="statsContainer" class="container mb-5" style="display: none;">
        <div class="card">
            <div class="card-header bg-primary text-white fw-bold">
                Estadísticas de la empresa
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Gráfica -->
                    <div class="col-md-8">
                        <canvas id="empresaChart" height="200"></canvas>
                    </div>
                    <!-- Preguntas -->
                    <div class="col-md-4">
                        <h5 class="fw-bold">Preguntas evaluadas:</h5>
                        <ol class="small">
                            <li>Relación de la Actividad con la carrera</li>
                            <li>Interacción con el tutor de la empresa</li>
                            <li>Asesoría por parte del tutor de la empresa</li>
                            <li>Asesoría por parte de la dirección de la empresa</li>
                            <li>Asesoría por parte de otros ingenieros en la empresa</li>
                            <li>Asesoría por parte del personal técnico en la empresa</li>
                            <li>Disponibilidad de materiales menores para la actividad</li>
                            <li>Disponibilidad de recursos informáticos para la actividad</li>
                            <li>Disponibilidad de equipo para la actividad</li>
                            <li>Seguridad para el desarrollo de actividades</li>
                            <li>Actitud respetuosa por parte del personal de la empresa</li>
                            <li>¿Recibiste remuneración económica?</li>
                            <li>¿Recomendarías esta empresa para que otros compañeros realicen una estancia profesional?</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const empresaDropdown = document.getElementById('empresaDropdown');
        const versionDropdown = document.getElementById('versionDropdown');
        const statsContainer = document.getElementById('statsContainer');

        let chartInstance = null;

        function mostrarGraficaSiListo() {
            const empresaSeleccionada = empresaDropdown.value === "ABB MÉXICO S.A. DE C.V.E";
            const versionSeleccionada = versionDropdown.value === "Versión 1.1 2024-2025";

            if (empresaSeleccionada && versionSeleccionada) {
                statsContainer.style.display = 'block';

                if (chartInstance) {
                    chartInstance.destroy();
                }

                const ctx = document.getElementById('empresaChart').getContext('2d');

                const data = {
                    labels: [
                        '1', '2', '3', '4', '5',
                        '6', '7', '8', '9', '10',
                        '11', '12', '13'
                    ],
                    datasets: [{
                        label: 'Evaluación (1-4)',
                        data: [3.8, 2.0, 3.9, 2.7, 1.5, 3.6, 2.9, 2.8, 4.0, 3.5, 2.7, 1.9, 2.0],
                        backgroundColor: 'rgba(0, 102, 255, 0.7)',
                        borderColor: 'rgba(0, 102, 255, 1)',
                        borderWidth: 1
                    }]
                };

                const options = {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 4,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Escala de evaluación'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                };

                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: options
                });

            } else {
                statsContainer.style.display = 'none';
            }
        }

        // Escucha cambios en ambos select
        empresaDropdown.addEventListener('change', mostrarGraficaSiListo);
        versionDropdown.addEventListener('change', mostrarGraficaSiListo);
    });
    </script>
@endsection
