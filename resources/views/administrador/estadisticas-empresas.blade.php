@extends('layouts.administrador')
@section('title','Estadísticas de las Empresas')

@section('content')
<h4 class="text-center fw-bold text-white py-3 shadow-sm" style="background: linear-gradient(90deg, #00124E, #003B95);">
    ESTADÍSTICAS DE LAS EMPRESAS REGISTRADAS
</h4>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="empresaDropdown" class="form-label fw-bold">Nombre de la empresa</label>
            <select class="form-select" id="empresaDropdown">
                <option selected disabled>Selecciona la empresa</option>
                <!-- Ejemplo fijo -->
                <option value="ejemplo" data-nombre="ABB MÉXICO S.A. DE C.V.E">ABB MÉXICO S.A. DE C.V.E</option>
                <!-- Opciones dinámicas -->
                @foreach($empresas as $empresa)
                    <option value="{{ $empresa->Id_Depn_Emp }}" data-nombre="{{ $empresa->Nombre_Depn_Emp }}">
                        {{ $empresa->Nombre_Depn_Emp }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="versionDropdown" class="form-label fw-bold">Versión</label>
            <select class="form-select" id="versionDropdown">
                <option selected disabled>Selecciona versión de cuestionario</option>
                <!-- Opciones dinámicas de versiones reales -->
                @foreach($versiones as $version)
                    <option value="{{ $version->Id_Version }}">
                        {{ $version->Num_Version }}
                    </option>
                @endforeach
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

    empresaDropdown.addEventListener('change', function () {
        // Limpiar versiones actuales
        const versionesOriginales = Array.from(versionDropdown.querySelectorAll('option[data-original]'));
        versionDropdown.innerHTML = '';
        versionDropdown.appendChild(document.createElement('option')).text = 'Selecciona versión de cuestionario';
        versionDropdown.options[0].selected = true;
        versionDropdown.options[0].disabled = true;

        // Re-agregar versiones reales
        versionesOriginales.forEach(opt => versionDropdown.appendChild(opt));

        // Si empresa ejemplo, agregar versión de ejemplo
        if (empresaDropdown.value === 'ejemplo') {
            const ejemploOption = document.createElement('option');
            ejemploOption.value = 'ejemplo';
            ejemploOption.text = 'Versión 1.1 2024-2025';
            versionDropdown.appendChild(ejemploOption);
        }

        mostrarGraficaSiListo();
    });

    async function mostrarGraficaSiListo() {
        const empresaId = empresaDropdown.value;
        const versionId = versionDropdown.value;

        if (!empresaId || !versionId) {
            statsContainer.style.opacity = 0;
            setTimeout(() => statsContainer.style.display = 'none', 500);
            return;
        }

        statsContainer.style.display = 'block';
        statsContainer.style.opacity = 0;

        let labels = Array.from({ length: 13 }, (_, i) => (i + 1).toString());
        let respuestas = [];
        let versionLabel = '';

        if (empresaId === 'ejemplo' && versionId === 'ejemplo') {
            respuestas = [3.8, 2.0, 3.9, 2.7, 1.5, 3.6, 2.9, 2.8, 4.0, 3.5, 2.7, 1.9, 2.0];
            versionLabel = 'Versión 1.1 2024-2025';
        } else {
            const response = await fetch(`/estadisticas-empresas/get-datos?empresa_id=${empresaId}&version_id=${versionId}`);
            const data = await response.json();
            respuestas = data.respuestas;
            versionLabel = data.version;
        }

        const tieneEvaluaciones = respuestas.some(r => r > 0);
        if (!tieneEvaluaciones) {
            respuestas = Array(13).fill(0);
            versionLabel += ' - Aún no hay evaluaciones';
        }

        if (chartInstance) chartInstance.destroy();

        const ctx = document.getElementById('empresaChart').getContext('2d');
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: [{ label: `Evaluación (1-4) - ${versionLabel}`, data: respuestas, backgroundColor: 'rgba(0, 102, 255, 0.7)', borderColor: 'rgba(0, 102, 255, 1)', borderWidth: 1 }] },
            options: {
                scales: { y: { beginAtZero: true, max: 4, ticks: { stepSize: 1 }, title: { display: true, text: 'Escala de evaluación' } } },
                plugins: { legend: { display: false } }
            }
        });

        setTimeout(() => { statsContainer.style.opacity = 1; }, 50);
    }

    versionDropdown.addEventListener('change', mostrarGraficaSiListo);
});
</script>
@endsection
