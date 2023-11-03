<?php

namespace App\Models;
use Auth;
use Carbon\Carbon;
use DB;
use Log;

use Illuminate\Database\Eloquent\Model;

class EpicrisisExamenPendiente extends Model
{
    public $table = "formulario_epicrisis_examenes_pendientes";
    protected $primaryKey = 'id';
    protected $fillable = [
        'visible'
    ];

    public $timestamps = false;

}
