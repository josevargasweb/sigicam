<?php

namespace App\Models\FormulariosGinecologia;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

class FormularioPartogramaTabla extends Model
{
    protected $table = 'formulario_partograma_tabla';
    public $timestamps = false;
    protected $primaryKey = 'id_formulario_partograma_tabla';


    public function examinador()
    {
        return  $this->belongsTo(Usuario::class,'examinador')->first();
    }
}
