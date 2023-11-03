<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;
use Carbon\Carbon;

class Indicacion extends Model{
	
	protected $table = "indicaciones";
	public $timestamps = false;
	protected $primaryKey = 'id';

	public static function indicacionesMedicas($caso){
		
		$inicio = Carbon::now()->startOfDay();
		$fin = Carbon::now()->endOfDay();
		return Indicacion::where('caso',$caso)->where('visible',true)
		->whereBetween('fecha_creacion', [$inicio, $fin])->get();
	}

	public static function todasIndicacionesMedicas($caso){
		return Indicacion::where('caso',$caso)->where('visible',true)->first();
	}

}

?>