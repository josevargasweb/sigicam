<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Ambulancia extends Model
{
    protected $table = 'ambulancias';

    protected $primaryKey = 'id';

    protected $fillable = [
        'patente', 'tipo_id','capacidad', 'estadoa_id', 'enuso', 'establecimiento_id', 
    ];

    

    public function tipo()
    {
        return $this->belongsTo('App\Models\TipoAmbulancia');
    }

    public function estado()
    {
        return $this->belongsTo('App\Models\EstadoAmbulancia');
    }

    public function establecimiento()
    {
        return $this->belongsTo('App\Models\Establecimiento');
    }

//    public function traslados()
//    {
//        return $this->hasMany('App\Models\Traslado');
//    }

    public function rutas()
    {
        return $this->hasMany('App\Models\Ruta');
    }
}
