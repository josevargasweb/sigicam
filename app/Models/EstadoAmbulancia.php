<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class EstadoAmbulancia extends Model
{
    protected $table = 'estado_ambulancias';


    protected $fillable = [
        'id', 'estado', 
    ];

    public function ambulancias()
    {
        return $this->hasMany('App\Models\Ambulancia');
    }
}
