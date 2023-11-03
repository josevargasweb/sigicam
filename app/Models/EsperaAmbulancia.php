<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class EsperaAmbulancia extends Model
{
    protected $table = 'espera_ambulancias';

    protected $fillable = [
        'id', 'hora_ambulancia_requerida', 'motivo', 'estado', 'paciente_id','establecimiento_id',
    ];

    

    public function paciente()
    {
        return $this->belongsTo('App\Models\Paciente');
    }

}


