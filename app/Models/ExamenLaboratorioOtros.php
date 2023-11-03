<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;
use Auth;
use Log;


class ExamenLaboratorioOtros extends Model
{
    protected $table = 'formulario_examenes_laboratorio_otros';
	public $timestamps = false;
	protected $primaryKey = 'id';

}
