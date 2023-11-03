<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Session;

class Restriccion extends Model
{
    protected $table = "restricciones";
	protected $fillable = [];
	protected $primaryKey = "id";

	public $timestamps = false;
}
