<?php

namespace App\Models;
use Auth;
use Carbon\Carbon;
use DB;
use Log;

use Illuminate\Database\Eloquent\Model;

class EpicrisisInterconsulta extends Model
{
    public $table = "formulario_epicrisis_interconsultas";
    protected $primaryKey = 'id';
    protected $fillable = [
        'visible'
    ];

    public $timestamps = false;

  
}
