<?php

namespace App\Models;
use Auth;
use Carbon\Carbon;
use DB;

use Illuminate\Database\Eloquent\Model;

class EpicrisisCuidado extends Model
{
    public $table = "formulario_epicrisis_cuidados";
    protected $primaryKey = 'id';
    protected $fillable = [
        'visible'
    ];

    public $timestamps = false;

    
}
