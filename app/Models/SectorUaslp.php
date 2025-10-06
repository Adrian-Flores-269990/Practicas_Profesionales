<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorUaslp extends Model
{
    use HasFactory;

    protected $table = 'sector_uaslp';
    protected $primaryKey = 'Id_UASLP';
    public $timestamps = false;

    protected $fillable = [
        'Area_Depto', 'Tipo_Entidad', 'Id_Entidad_Academica'
    ];
}
