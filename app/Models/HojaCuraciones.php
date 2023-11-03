<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;

class HojaCuraciones extends Model
{
    protected $table = 'formulario_hoja_curaciones_valoracion_herida';
	public $timestamps = false;
	protected $primaryKey = 'id';


	public static function calcular($herida){


		$valorTotal = 0;

		foreach ($herida as $key => $h) {
			if(in_array($key, ["aspecto","exudado_cantidad","exudado_calidad","edema","piel_circundante", "mayor_extension", "profundidad", "tejido_esfacelado", "dolor", "tejido_granulatorio"]) ){
				if(in_array($h, ['Eritematoso', '0 - 1 cm', '0', 'Sin exudado', 'Ausente', 'Ausente', '< 25%', '100 - 75%', '0 - 1','Sana']) ){
					$valorTotal += 1;
				}else if(in_array($h, ['Enrojecido','> 1 - 3 cm','< 1 cm','Seroso','Escaso','< 75 - 50%','+','2 - 3','Descamada']) ){
					$valorTotal += 2;
				}else if(in_array($h, ['Amarillo pálido','> 3 - 6 cm','1 - 3 cm','Turbio','Moderado','25 - 50%','< 50 - 25%','++','4 - 6','Eritematosa']) ){
					$valorTotal += 3;
				}else{
					$valorTotal += 4;
				}
			}
		}

		if($valorTotal <= 15 ){$valorTotal = $valorTotal." (Grado 1)";
		}elseif ($valorTotal > 15 && $valorTotal <= 21){ $valorTotal = $valorTotal." (Grado 2)";
		}elseif($valorTotal > 21 && $valorTotal <= 27){ $valorTotal = $valorTotal." (Grado 3)";
		}else{$valorTotal = $valorTotal." (Grado 4)";}

		return $valorTotal;
	}

	public static function homologarAspecto($aspecto){
		$array = [
			'1' => 'Eritematoso',
			'2' => 'Enrojecido',
			'3' => 'Amarillo pálido',
			'4' => 'Necrótico'
		];

		if(array_key_exists($aspecto, $array)){
			return $array[$aspecto];
		}else{
			return null;
		}		
	}

	public static function homologarMayorExtension($extension){
		$array = [
			'1' => '0 - 1 cm',
			'2' => '> 1 - 3 cm',
			'3' => '> 3 - 6 cm',
			'4' => '> 6 cm'
		];

		if(array_key_exists($extension, $array)){
			return $array[$extension];
		}else{
			return null;
		}		
	}

	public static function homologarProfundidad($prof){
		$array = [
			'1' => '0',
			'2' => '< 1 cm',
			'3' => '1 - 3 cm',
			'4' => '> 3 cm'
		];

		if(array_key_exists($prof, $array)){
			return $array[$prof];
		}else{
			return null;
		}	
	}
	public static function homologarExudadoCantidad($var){
		$array = [
			'1' => 'Ausente',
			'2' => 'Escaso',
			'3' => 'Moderado',
			'4' => 'Abundante'
		];

		if(array_key_exists($var, $array)){
			return $array[$var];
		}else{
			return null;
		}	
	}
	public static function homologarExudadoCalidad($var){
		$array = [
			'1' => 'Sin exudado',
			'2' => 'Seroso',
			'3' => 'Turbio',
			'4' => 'Purulento'
		];

		if(array_key_exists($var, $array)){
			return $array[$var];
		}else{
			return null;
		}	
	}
	public static function homologarEscafelado($var){
		$array = [
			'1' => 'Ausente',
			'2' => '< 25%',
			'3' => '25 - 50%',
			'4' => '> 50%'
		];

		if(array_key_exists($var, $array)){
			return $array[$var];
		}else{
			return null;
		}	
	}
	public static function homologarGranulatorio($var){
		$array = [
			'1' => '100 - 75%',
			'2' => '< 75 - 50%',
			'3' => '< 50 - 25%',
			'4' => '< 25%'
		];

		if(array_key_exists($var, $array)){
			return $array[$var];
		}else{
			return null;
		}	
	}
	public static function homologarEdema($var){
		$array = [
			'1' => 'Ausente',
			'2' => '+',
			'3' => '++',
			'4' => '+++'
		];

		if(array_key_exists($var, $array)){
			return $array[$var];
		}else{
			return null;
		}	
	}
	public static function homologarDolor($var){
		$array = [
			'1' => '0 - 1',
			'2' => '2 - 3',
			'3' => '4 - 6',
			'4' => '7 - 10'
		];

		if(array_key_exists($var, $array)){
			return $array[$var];
		}else{
			return null;
		}	
	}
	public static function homologarPielCircundante($var){
		$array = [
			'1' => 'Sana',
			'2' => 'Descamada',
			'3' => 'Eritematosa',
			'4' => 'Macerada'
		];

		if(array_key_exists($var, $array)){
			return $array[$var];
		}else{
			return null;
		}	
	}
	
	
	

}
