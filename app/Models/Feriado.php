<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
class Feriado extends Model{
	
	protected $table = "feriados";
	public $timestamps = false;
	protected $primaryKey = "id_feriado";
}
 
?>