<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraIngenieria extends Model
{
    protected $table = 'carrera_ingenieria';
    protected $primaryKey = 'Id_Carrera';
    public $timestamps = false;

    protected $fillable = [
        'Descripcion_Capitalizadas',
    ];
}
