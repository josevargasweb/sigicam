<?php

namespace App\Models\FormulariosGinecologia;

use Illuminate\Database\Eloquent\Model;

class FormularioPartograma extends Model
{
    protected $table = 'formulario_partograma';
    public $timestamps = false;
    protected $primaryKey = 'id_formulario_partograma';

    public function tabla()
    {
        return $this->hasMany(FormularioPartogramaTabla::class,'id_formulario_partograma')->orderBy('id_formulario_partograma_tabla');;
    }

    public function evoluciones()
    {
        return $this->hasMany(FormularioPartogramaEvolucion::class,'id_formulario_partograma')->orderBy('id_formulario_partograma_evolucion');;
    }

}
