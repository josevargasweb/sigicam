<?php

namespace App\Models\FormulariosGinecologia;

use Illuminate\Database\Eloquent\Model;

class FormularioSolicitudTransfusion extends Model
{
    protected $table = 'formulario_solicitud_transfusion';
    public $timestamps = false;
    protected $primaryKey = 'id_formulario_solicitud_transfusion';


    public function instalaciones()
    {
        return $this->hasMany(FormularioSolicitudTransfusionInstalacion::class,'id_formulario_solicitud_transfusion')->orderBy('id_formulario_solicitud_transfusion_instalacion');;
    }

}
