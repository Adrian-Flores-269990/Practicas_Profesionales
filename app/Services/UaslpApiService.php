<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UaslpApiService
{
    private string $apiKey = 'ag4e7/@/$5354';
    private string $baseUrl = 'https://servicios.ing.uaslp.mx/web_services/apis';

    public function login($clave, $password, $tipo)
    {
        $endpoint = "{$this->baseUrl}/login.php";

        $payload = [
            "key_api" => $this->apiKey,
            "clave" => $clave,
            "contra" => $password,
            "tipo" => $tipo
        ];

        $response = Http::withoutVerifying()->post($endpoint, $payload);

        return $response->json();
    }

    public function obtenerDatos($clave, $tipo)
    {
        $endpoint = "{$this->baseUrl}/practicas_profesionales/pp_get_datos.php";

        $payload = [
            "key_api" => $this->apiKey,
            "clave" => $clave,
            "tipo" => $tipo
        ];

        $response = Http::withoutVerifying()->post($endpoint, $payload);

        return $response->json();
    }
}
