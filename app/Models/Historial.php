<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model{
	
	public function scopeActuales($query){
		$query = $query->where("rk", "=", "1");
		return $query;
	}

}

