<?php

namespace App\Models\FormulariosGinecologia;

use Illuminate\Database\Eloquent\Model;

class FormularioInterrupcionEmbarazo extends Model
{
    protected $table = 'formulario_interrupcion_embarazo';
    public $timestamps = false;
    protected $primaryKey = 'id_formulario_interrupcion_embarazo';
}
