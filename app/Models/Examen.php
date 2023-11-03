<?php
namespace App\models;
use DB;
use Illuminate\Database\Eloquent\Model;
class Examen extends Model{
	
	protected $table = "examenes";
	public $timestamps = true;
	protected $primaryKey = "id";

	public static function cantidadDeExpamenesPendientes(){
		/* count( distinct c.id)*/
		return DB::select(DB::raw("select count(*)
		from casos c
		inner join examenes e on e.caso = c.id 
		where e.pendiente is true and e.visible = true and c.fecha_termino is null"));

	}
}
 
?>