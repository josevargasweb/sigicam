<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use DB;
use Auth;
use Log;

class GesNotificacion extends Model
{

    protected $table = "formulario_ges_notificacion";
    protected $primaryKey = "id";
    public $timestamps = false;


}
