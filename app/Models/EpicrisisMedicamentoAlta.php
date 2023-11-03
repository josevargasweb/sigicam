<?php

namespace App\Models;
use Auth;
use Carbon\Carbon;
use DB;
use Log;

use Illuminate\Database\Eloquent\Model;

class EpicrisisMedicamentoAlta extends Model
{
    public $table = "formulario_epicrisis_medicamentos_alta";
    protected $primaryKey = 'id';
    protected $fillable = [
        'visible'
    ];

    public $timestamps = false;

}
