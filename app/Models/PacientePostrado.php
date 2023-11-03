<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacientePostrado extends Model
{
    protected $table = 'formulario_paciente_postrado';
	public $timestamps = false;
	protected $primaryKey = 'id_formulario_paciente_postrado';
}