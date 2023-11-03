<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;
use Carbon\Carbon;

class HojaCuracionesSimple extends Model
{
    protected $table = 'formulario_hoja_curaciones_curaciones';
	public $timestamps = false;
	protected $primaryKey = 'id';

  public static function alertaCuracionesSimple($caso){
    $inicio = Carbon::now()->startOfDay();
    $fin = Carbon::now()->endOfDay();
    $curaciones_simples = HojaCuracionesSimple::where('caso',$caso)->where('visible',true)->where('tipo_curacion', '=','Simple')
    ->whereBetween('proxima_curacion', [$inicio, $fin])->get()->count();
    $curaciones_avanzadas = HojaCuracionesSimple::where('caso',$caso)->where('visible',true)->where('tipo_curacion', '=','Avanzada')
    ->whereBetween('proxima_curacion', [$inicio, $fin])->get()->count();
    return ["simples" => $curaciones_simples, "avanzadas" => $curaciones_avanzadas];
  }

}
