<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DependenciaEmpresaSolicitud extends Model
{
    use HasFactory;

    protected $table = 'dependenciaempresasolicitud';
    protected $primaryKey = 'Id_DPMS';
    public $timestamps = false;

    protected $fillable = [
        'Id_Solicitud_FPP01', 'Id_Depend_Emp', 'Id_Privado',
        'Id_Publico', 'Id_UASLP', 'Id_Mercado', 'Porcentaje'
    ];
}
