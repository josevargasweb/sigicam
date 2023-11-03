<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ComplejidadAreaFuncional;

class Complejidad_servicio extends Model
{
    //
    protected $table = "complejidad_servicio";
    public $timestamps = false;
    protected $primaryKey = 'id_complejidad';

    public function complejidad_area_funcional (){
    	return $this->belongsTo(ComplejidadAreaFuncional::class,'id_complejidad');
    }

}