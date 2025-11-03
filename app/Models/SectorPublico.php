<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorPublico extends Model
{
    use HasFactory;

    protected $table = 'sector_publico';
    protected $primaryKey = 'Id_Publico';
    public $timestamps = false;

    protected $fillable = [
        'Area_Depto',
        'Ambito'
    ];
}
