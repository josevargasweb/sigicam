<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntervencionQuirurgica extends Model{
	
	protected $table = "intervencion_quirurgica";
	public $timestamps = false;
	protected $primaryKey = 'id_intervencion_quirurgica';

}

?>