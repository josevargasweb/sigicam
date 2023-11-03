<?php

namespace App\Models\FormulariosGinecologia;

use Illuminate\Database\Eloquent\Model;

class FormularioDocumentoAlta extends Model
{
    protected $table = 'formulario_documentos_alta';
    public $timestamps = false;
    protected $primaryKey = 'id_formulario_documentos_alta';
}
