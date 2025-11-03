<?php

namespace App\Observers;

use App\Models\SolicitudFPP01;
use App\Models\EstadoProceso;

class SolicitudFPP01Observer
{
    /**
     * Cuando una solicitud se actualiza (aprobado/rechazado)
     */
    public function updated(SolicitudFPP01 $solicitud)
    {
        $claveAlumno = $solicitud->Clave_Alumno;

        // 🔹 Departamento
        if ($solicitud->Estado_Departamento === 'aprobado') {
            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'realizado']);
        } elseif ($solicitud->Estado_Departamento === 'rechazado') {
            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);
        }

        // 🔹 Encargado
        if ($solicitud->Estado_Encargado === 'aprobado') {
            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'realizado']);
        } elseif ($solicitud->Estado_Encargado === 'rechazado') {
            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)')
                ->update(['estado' => 'pendiente']);
        }

        // 🔹 Si ambos aprobaron → siguiente fase
        if ($solicitud->Estado_Departamento === 'aprobado' && $solicitud->Estado_Encargado === 'aprobado') {
            EstadoProceso::where('clave_alumno', $claveAlumno)
                ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                ->update(['estado' => 'proceso']);
        }
    }
}
