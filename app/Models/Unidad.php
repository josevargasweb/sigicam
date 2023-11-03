<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use App\Models\UnidadEnEstablecimiento;
use DB;
use Log;


class Unidad extends Model{
	protected $table = "unidades";
	public function unidadesEnEstablecimientos(){
		return $this->belongsToMany('App\Models\UnidadEnEstablecimiento', 'servicios_ofrecidos', 'unidad', 'unidad_en_establecimiento');
	}

	public function unidadesQueOfrecen(){
		return $this->belongsToMany('App\Models\UnidadEnEstablecimiento', 'servicios_ofrecidos', 'unidad', 'unidad_en_establecimiento');
	}

	public function unidadesQueReciben(){
		return $this->belongsToMany('App\Models\UnidadEnEstablecimiento', 'servicios_recibidos', 'unidad', 'unidad_en_establecimiento');
	}

	public static function getIdUnidad($unidad){
		return self::where("alias", "=", $unidad)->first()->id;
	}

	public static function getAllServicios(){
		$response=array();
		$servicios=self::all();
		foreach ($servicios as $servicio) {
			$response[$servicio->id]=$servicio->nombre;
		}
		return $response;
	}

	public static function getAlias($id){
		return self::find($id)->alias;
	}

	public static function unidades(){
		$recibidos = DB::table('unidades')
                        ->select('unidades.id as id', 'unidades.nombre as nombre')
                        ->groupBy('unidades.id', 'unidades.nombre')
						->get();

		$unidades = [];
		foreach($recibidos as $recv){
			$unidades[$recv->id] = $recv->nombre;
		}

		return $unidades;
	}

	public static function tipoUnidad($id){
		$array["NEO"] = ['191','193'];//neo
		$array["ADULTO"] = ['176','200','180','179','199','201','184','187','190','181','183','182','186','185','198','194','197','195','178'];//adulto
		$array["PED"] = ['188','189','196'];//pediatrica
		$array["NEO_UCI"] = ['192'];//neo uci
		$array["ADULTO_UCI"] = ['177','202'];//adulto uci
		$array["PED_UCI"] = [];//pediatria uci

		$unidad = DB::select("select * from unidades_en_establecimientos u
		join tipos_unidad t on t.id = u.tipo_unidad
		where u.id = $id");
		
		return (isset($unidad[0]->nombre))?$unidad[0]->nombre:"ADULTO";
	}

	public static function calcularDotacion($id,$basica,$media,$critica){

		$valores ["ADULTO"] ["BASICA"] = [24,1,2,0];
		$valores ["ADULTO"] ["MEDIA"] = [24,2,3,0];
		$valores ["ADULTO_UCI"]["CRITICA"]= [6,2,3,0];//con UPC
		$valores ["ADULTO"] ["CRITICA"] = [12,2,3,0];//sin UPC

		$valores ["PED"] ["BASICA"] = [24,2,3,0];
		$valores ["PED"] ["MEDIA"] = [24,2,3,0];
		//$valores ["PED"] ["CRITICA"] = [6,3,2,0];//con UPC
		$valores ["PED"] ["CRITICA"] = [12,2,3,0];//sin UPC

		$valores ["NEO"]["BASICA"] = [24,0,2,3];
		$valores ["NEO"]["MEDIA"] = [24,0,2,3];
		$valores ["NEO_UCI"]["CRITICA"] = [6,0,2,2];//con UPC
		$valores ["NEO"]["CRITICA"] = [12,0,3,2];//sin UPC

		$tipo = Unidad::tipoUnidad($id);
		
		$enfermeras_B = 0;
		$enfermeras_M = 0;
		$enfermeras_C = 0;
		$tens_B = 0;
		$tens_M = 0;
		$tens_C = 0;
		$necesita = " Enfermeras";
		
		if($tipo == "PED" || $tipo == "ADULTO" || $tipo == "ADULTO_UCI"){
			
			if($basica > 0){
				if($tipo == "ADULTO_UCI"){
					$enfermeras_B =  (intval($valores ["ADULTO"] ['BASICA'][1]) * intval($basica)) /intval($valores ["ADULTO"] ['BASICA'][0]);
					$tens_B = (intval($valores ["ADULTO"] ['BASICA'][2]) * intval($basica)) /intval($valores ["ADULTO"] ['BASICA'][0]);
				}else{
					$enfermeras_B =  (intval($valores [$tipo] ['BASICA'][1]) * intval($basica)) /intval($valores [$tipo] ['BASICA'][0]);
					$tens_B =  (intval($valores [$tipo] ['BASICA'][2]) * intval($basica)) /intval($valores [$tipo] ['BASICA'][0]);
				}				
			}

			if($media > 0){
				if($tipo == "ADULTO_UCI"){
					$enfermeras_M =  (intval($valores ["ADULTO"] ['MEDIA'][1]) * intval($media)) /intval($valores ["ADULTO"] ['MEDIA'][0]);
					$tens_M =  (intval($valores ["ADULTO"] ['MEDIA'][2]) * intval($media)) /intval($valores ["ADULTO"] ['MEDIA'][0]);
				}else{
					$enfermeras_M =  (intval($valores [$tipo] ['MEDIA'][1]) * intval($media)) /intval($valores [$tipo] ['MEDIA'][0]);
					$tens_M =  (intval($valores [$tipo] ['MEDIA'][2]) * intval($media)) /intval($valores [$tipo] ['MEDIA'][0]);
				}				
			}
			
			if($critica > 0){
				$enfermeras_C =  (intval($valores[$tipo]["CRITICA"][1]) * intval($critica)) /intval($valores [$tipo] ['CRITICA'][0]);
				$tens_C =  (intval($valores[$tipo]["CRITICA"][2]) * intval($critica)) /intval($valores [$tipo] ['CRITICA'][0]);
			}
		}else{
			$necesita = " Matronas";

			if($basica > 0){
				$enfermeras_B =  (intval($valores ["NEO"] ['BASICA'][3]) * intval($basica)) /intval($valores ["NEO"] ['BASICA'][0]);	
				$tens_B =  (intval($valores ["NEO"] ['BASICA'][2]) * intval($basica)) /intval($valores ["NEO"] ['BASICA'][0]);				
			}

			if($media > 0){
				$enfermeras_M =  (intval($valores ["NEO"] ['MEDIA'][3]) * intval($media)) /intval($valores ["NEO"] ['MEDIA'][0]);
				$tens_M =  (intval($valores ["NEO"] ['MEDIA'][2]) * intval($media)) /intval($valores ["NEO"] ['MEDIA'][0]);
			}
			
			if($critica > 0){
				$enfermeras_C =  (intval($valores [$tipo] ['CRITICA'][3]) * intval($critica) ) /intval($valores [$tipo] ['CRITICA'][0]);
				$tens_C =  (intval($valores [$tipo] ['CRITICA'][2]) * intval($critica) ) /intval($valores [$tipo] ['CRITICA'][0]);
			}
		}
		
		return "Debido a la categorización de pacientes, esta unidad necesita ".round(($enfermeras_B+$enfermeras_M+$enfermeras_C),2).$necesita." y ".round(($tens_B+$tens_M+$tens_C),2)." Tens.";
	}
}
