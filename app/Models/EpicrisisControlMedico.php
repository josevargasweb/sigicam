<?php

namespace App\Models;
use Auth;
use Carbon\Carbon;
use DB;
use Log;

use Illuminate\Database\Eloquent\Model;

class EpicrisisControlMedico extends Model
{
    public $table = "formulario_epicrisis_controles_medicos";
    protected $primaryKey = 'id';
    protected $fillable = [
        'visible'
    ];

    public $timestamps = false;

}
