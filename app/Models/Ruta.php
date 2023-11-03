<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $table = 'rutas';


    protected $fillable = [
        'id', 'hospital_origen', 'hora_salida', 'hospital_destino', 'hora_llegada_API', 'paciente_id' , 'ambulancia_id',
    ];

    public function ambulancia()
    {
        return $this->belongsTo('App\Models\Ambulancia');
    }

    
}
