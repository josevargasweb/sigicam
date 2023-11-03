<?php

namespace App\Models\FormulariosGinecologia;

use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;

class FormularioPartogramaEvolucion extends Model
{
    protected $table = 'formulario_partograma_evolucion';
    public $timestamps = false;
    protected $primaryKey = 'id_formulario_partograma_evolucion';


    public function usuarioResponsable(){
        return  $this->belongsTo(Usuario::class,'usuario_responsable')->first();
    }

}
