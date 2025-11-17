<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraIngenieria extends Model
{
    protected $table = 'carrera_ingenieria';
    protected $primaryKey = 'Id_Carrera_I';
    public $timestamps = false;

    protected $fillable = [
        'Id_Carrera_I',
        'Descripcion_Mayúsculas',
        'Descripcion_Capitalizadas',
        'Clave_Area',
    ];

    // Una carrera pertenece a un área
    public function area()
    {
        return $this->belongsTo(Area::class, 'Clave_Area', 'Id_Area');
    }

    // Una carrera tiene muchos requisitos
    public function requisitos()
    {
        return $this->hasMany(RequisitoCarrera::class, 'Id_Carrera_I', 'Id_Carrera_I');
    }
}
