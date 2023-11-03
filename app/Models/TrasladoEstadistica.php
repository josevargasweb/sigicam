<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class TrasladoEstadistica extends Model{
	
    protected $table = "traslado_estadistica";
    protected $primaryKey = "id";
	public $timestamps = false;
}

?>