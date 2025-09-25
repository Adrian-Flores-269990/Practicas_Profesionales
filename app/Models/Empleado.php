<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Empleado extends Authenticatable
{
    protected $table = 'encargado';  // Nombre exacto de la tabla
    protected $primaryKey = 'Id_Encargado';
    public $timestamps = false;

    protected $fillable = [
        'Nombre','RPE','Area','Carrera','Cargo','Correo_Electronico','Telefono','Contrasena'
    ];

    protected $hidden = [
        'Contrasena',
    ];

    // Indica a Laravel que la contraseña está en el campo 'Contrasena'
    public function getAuthPassword() {
        return $this->Contrasena;
    }
}
