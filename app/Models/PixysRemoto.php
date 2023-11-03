<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
class PixysRemoto extends Model{

	protected $connection = 'sqlsrv';
	protected $table = "ADT3";
	
	
	//protected $table = "integracion2";
	public $timestamps = false;
	protected $primaryKey = "correlativo";
    

}

?>
