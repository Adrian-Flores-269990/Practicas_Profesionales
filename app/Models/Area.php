<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'Id_Area';
    public $timestamps = false;

    protected $fillable = [
        'Id_Area',
        'Area',
    ];

    // Un área tiene muchas carreras
    public function carreras()
    {
        return $this->hasMany(CarreraIngenieria::class, 'Clave_Area', 'Id_Area');
    }
}