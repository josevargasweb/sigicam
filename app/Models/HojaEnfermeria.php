<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeria extends Model
{
    public $table = "formulario_hoja_enfermeria";
    protected $primaryKey = 'id_formulario_hoja_enfermeria';
    protected $fillable = [
        
    ];

    public $timestamps = false;
    /* public $incrementing = false; */
}
