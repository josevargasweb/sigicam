<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use DB;
use Auth;
use Log;

class MedicoInterconsulta extends Model
{

    protected $table = "formulario_medico_interconsulta";
    protected $primaryKey = "id";
    public $timestamps = false;


}
