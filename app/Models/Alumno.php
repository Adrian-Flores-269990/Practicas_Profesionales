<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Alumno extends Authenticatable
{
    protected $table = 'alumno';
    protected $primaryKey = 'clave_alumno';
    public $timestamps = false;

    protected $fillable = [
        'Clave_Alumno',
        'Nombre',
        'ApellidoP_Alumno',
        'ApellidoM_Alumno',
        'Semestre',
        'Carrera',
        'TelefonoCelular',
        'CorreoElectronico',
        'Clave_Materia',
        'Clave_Carrera',
        'Clave_Area'
    ];

    // protected $hidden = [
    //     'password',
    // ];

    // Esto asegura que Auth use el campo correcto como contraseña
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudFPP01::class, 'Clave_Alumno', 'Clave_Alumno');
    }
}
