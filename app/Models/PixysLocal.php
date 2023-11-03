<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
class PixysLocal extends Model{

	//protected $connection = 'sqlsrv';
	//protected $table = "ADT3";
	protected $table = "integracion";
	public $timestamps = false;
	protected $primaryKey = "correlativo";
    

}

?>