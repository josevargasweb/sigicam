<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EvolucionCaso;
use App\Models\Complejidad_servicio;
use App\Models\AreaFuncional;

class ComplejidadAreaFuncional extends Model
{
    protected $table = "complejidad_area_funcional";
    public $timestamps = false;
    protected $primaryKey = "id_complejidad_area_funcional";
    protected $fillable = [
    	'id_complejidad',
    	'id_area_funcional'
    ];

    public function evolucionCaso(){
    	return $this->hasOne(EvolucionCaso::class, 'id_complejidad_area_funcional');
    }

    public function servicios(){
    	return $this->belongsTo(Complejidad_servicio::class,'id_complejidad');
    }

    public function area(){
    	return $this->belongsTo(AreaFuncional::class, 'id_area_funcional');
    }
}
