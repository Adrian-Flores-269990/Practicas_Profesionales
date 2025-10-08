<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud.
     */
    public function authorize(): bool
    {
        // Cambia a true si todos los usuarios pueden hacer esta solicitud
        return true;
    }

    /**
     * Reglas de validación para la solicitud.
     */
    public function rules(): array
    {
        return [
            // Campos de solicitud_fpp01
            'Fecha_Solicitud' => 'required|date',
            'Numero_Creditos' => 'required|integer|min:1',
            'Clave_Alumno' => 'required|exists:alumnos,Clave_Alumno', // asumiendo que hay tabla alumnos
            'Fecha_Inicio' => 'required|date|after_or_equal:Fecha_Solicitud',
            'Fecha_Termino' => 'required|date|after:Fecha_Inicio',
            'Nombre_Proyecto' => 'required|string|max:255',
            'Actividades' => 'nullable|string',

            

            // Campos del asesor externo / encargado
            'encargado_nombre' => 'required|string|max:100',
            'encargado_apellido_paterno' => 'nullable|string|max:100',
            'encargado_apellido_materno' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'encargado_puesto' => 'required|string|max:100',
            'encargado_correo' => 'required|email|max:150',
            'encargado_telefono' => 'nullable|string|max:20',
        ];
    }

    /**
     * Mensajes personalizados para validación.
     */
    public function messages(): array
    {
        return [
            'Fecha_Solicitud.required' => 'La fecha de solicitud es obligatoria.',
            'Numero_Creditos.required' => 'El número de créditos es obligatorio.',
            'Clave_Alumno.required' => 'La clave del alumno es obligatoria.',
            'Clave_Alumno.exists' => 'El alumno seleccionado no existe.',
            'Fecha_Inicio.after_or_equal' => 'La fecha de inicio debe ser igual o posterior a la fecha de solicitud.',
            'Fecha_Termino.after' => 'La fecha de término debe ser posterior a la fecha de inicio.',
            'Nombre_Proyecto.required' => 'El nombre del proyecto es obligatorio.',
            'encargado_nombre.required' => 'El nombre del encargado es obligatorio.',
            'encargado_puesto.required' => 'El puesto del encargado es obligatorio.',
            'encargado_correo.required' => 'El correo del encargado es obligatorio.',
            'encargado_correo.email' => 'El correo del encargado debe ser un email válido.',
        ];
    }
}
