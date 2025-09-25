<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Alumno extends Authenticatable
{
    protected $table = 'alumno';
    protected $primaryKey = 'clave_alumno';
    public $timestamps = false;

    protected $fillable = [
        'nombre_alumno',
        'apellidop_alumno',
        'apellidom_alumno',
        'semestre',
        'carrera',
        'telfono_celular',
        'correo_electronico',
        'password',
        'cve_alumno',
        'rpe'
    ];

    protected $hidden = [
        'password',
    ];

    // Esto asegura que Auth use el campo correcto como contraseña
    public function getAuthPassword()
    {
        return $this->password;
    }
}
