<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
class Liberacion extends Model{
	protected $table = "liberaciones";
	public function camas(){
		return $this->hasMany("App\Models\Cama", "cama", "id");
	}
}