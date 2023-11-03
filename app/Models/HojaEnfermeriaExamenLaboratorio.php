<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaExamenLaboratorio extends Model
{
    public $table = "formulario_hoja_enfermeria_examenes_laboratorio";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}
