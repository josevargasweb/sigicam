<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TipoAmbulancia extends Model
{
    protected $table = 'tipo_ambulancias';

    protected $fillable = [
        'id','nombre',
    ];

    public function ambulancias()
    {
        return $this->hasMany('App\Models\Ambulancia');
    }
}
