<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Auth;

class IEOtros extends Model
{
    protected $table = 'formulario_ie_otros';
	public $timestamps = false;
	protected $primaryKey = 'id';

    public static function crearNuevo($request, $modificar){

        $otro = new IEOtros;
        DB::beginTransaction();
        $otro->caso = strip_tags($request->idCaso);

        if($modificar != null || $modificar != ""){
			$otro->id_anterior = $modificar->id;
		}

        $otro->usuario_responsable = Auth::user()->id;
        $otro->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");

        $cateteres = $request->cateteres;
        $otro->branulas1 = false;
        $otro->branulas2 = false;
        $otro->s_foley = false;
        $otro->sng = false;
        $otro->cvc = false;
        $otro->otro_cateter = false;
        if($cateteres == null){
            $otro->branulas1 = false;
            $otro->branulas2 = false;
            $otro->s_foley = false;
            $otro->sng = false;
            $otro->cvc = false;
            $otro->otro_cateter = false;
        }else{
            foreach ($cateteres as $ca) {
                if($ca == '0'){
                    $otro->branulas1 = true;
                }else
                if($ca == '1'){
                    $otro->branulas2 = true;
                }else
                if($ca == '2'){
                    $otro->s_foley = true;
                }else
                if($ca == '3'){
                    $otro->sng = true;
                }else
                if($ca == '4'){
                    $otro->cvc = true;
                }else
                if($ca == '5'){
                    $otro->otro_cateter = true;
                }
            }
        }
        $otro->detalle_otro_cateter = strip_tags($request->detalleOtroCateter);
        $otro->detalle_b1 = strip_tags($request->detalleB1);
        $otro->detalle_b2 = strip_tags($request->detalleB2);
        $otro->detalle_foley = strip_tags($request->detalleF);
        $otro->detalle_sng = strip_tags($request->detalleSng);
        $otro->detalle_cvc = strip_tags($request->detalleCvc);
        $otro->examenes = strip_tags($request->examenes);
        $otro->indicacionenfermera = strip_tags($request->indicacionMedico);

        $otro->save();

        return $otro;
    }
}
