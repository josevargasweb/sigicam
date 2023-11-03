<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PermisosEstablecimiento extends Model{
	protected $table = "permisos_establecimientos";

	public function establecimiento(){
		return $this->belongsTo("App\Models\Establecimiento", "establecimiento", "id");
	}
}