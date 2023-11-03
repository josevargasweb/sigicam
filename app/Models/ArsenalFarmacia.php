<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Carbon\Carbon;


class ArsenalFarmacia extends Model{

    protected $table = "arsenal_farmacia";
	public $timestamps = false;
	protected $primaryKey = 'id';

	// protected $appends = ['nombreunidad'];

	// public function getNombreunidadAttribute(){
	// 	return "{$this->nombre} ({$this->unidad_medida})"; 
	// }
}

?>