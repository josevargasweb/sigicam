<?php

namespace App\Models\FormulariosGinecologia;

use Illuminate\Database\Eloquent\Model;

class FormularioProtocoloParto extends Model
{
    protected $table = 'formulario_protocolo_parto';
    public $timestamps = false;
    protected $primaryKey = 'id_formulario_protocolo_parto';
}
