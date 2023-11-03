<?php

namespace App\Models;
use Auth;
use Carbon\Carbon;
use DB;
use Log;

use Illuminate\Database\Eloquent\Model;

class EpicrisisEducacionRealizada extends Model
{
    public $table = "formulario_epicrisis_educaciones_realizadas";
    protected $primaryKey = 'id';
    protected $fillable = [
        'visible'
    ];

    public $timestamps = false;

  
}
