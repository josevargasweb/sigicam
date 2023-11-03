<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaInterconsulta extends Model
{
    public $table = "formulario_hoja_enfermeria_interconsulta";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}
