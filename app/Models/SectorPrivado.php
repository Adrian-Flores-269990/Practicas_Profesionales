<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorPrivado extends Model
{
    use HasFactory;

    protected $table = 'sector_privado';
    protected $primaryKey = 'Id_Privado';
    public $timestamps = false;

    protected $fillable = [
        'Area_Depto', 'Num_Trabajadores', 'Actividad_Giro',
        'Razon_Social', 'Emp_Outsourcing', 'Razon_Social_Outsourcing'
    ];
}
