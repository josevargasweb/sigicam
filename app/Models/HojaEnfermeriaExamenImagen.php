<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaExamenImagen extends Model
{
    public $table = "formulario_hoja_enfermeria_examenes_imagen";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}
