<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeEgreso extends Model{
	
	protected $table = "informe_egreso";
	public $timestamps = false;
	protected $primaryKey = 'id_informe_egreso';

}

?>