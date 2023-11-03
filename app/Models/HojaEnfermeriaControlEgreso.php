<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaControlEgreso extends Model
{
    public $table = "formulario_hoja_enfermeria_controles_egresos";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}
