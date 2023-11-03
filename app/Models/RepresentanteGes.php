<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use DB;
use Auth;
use Log;

class RepresentanteGes extends Model
{

    protected $table = "representante_ges";
    protected $primaryKey = "id";
    public $timestamps = false;


}
