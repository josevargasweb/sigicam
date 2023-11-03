<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use StdClass;
use App\Models\Establecimiento;
use App\Models\Consultas;
use App\Models\EvolucionCaso;
use App\Models\Especialidades;
use Carbon\Carbon;
use View;
use App\Models\UnidadEnEstablecimiento;
use Auth;
use Session;
use Excel;
use PHPExcel_Calculation;

use PHPExcel;
use PHPExcel_IOFactory;
use DB;
use Debugbar;
use Log;
use Config;

class InfoHoja extends StdClass{
	public $nombre;
	public $enfermera;
	protected $mes;
	public $anno;
	public $categorias = array();
	public $datos = array();
	public $max_dia = 1;
	protected $hospital;
	protected $servicio;
	public $establecimiento_original;
	public $error = "";
	protected $id_establecimiento;
	protected $id_servicio;

	public $error_unidad;

	public $A1;
	public $A2;
	public $A3;

	public $B1;
	public $B2;
	public $B3;

	public $C1;
	public $C2;
	public $C3;

	public $D1;
	public $D2;
	public $D3;

	public static $meses = [
		1 => "Enero",
		2 => "Febrero",
		3 => "Marzo",
		4 => "Abril",
		5 => "Mayo",
		6 => "Junio",
		7 => "Julio",
		8 => "Agosto",
		9 => "Septiembre",
		10 => "Octubre" ,
		11 => "Noviembre" ,
		12 => "Diciembre" ,
	];



	protected function toAscii($str) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
		$hash = hash("crc32", $str);
		return "{$hash}-{$clean}";
	}

	public function nombreId(){
		return $this->toAscii($this->nombre);
	}

	public function array_dias(){
		$r = array();
		foreach($this->rangoDias() as $dia){
			foreach($this->categorias as $cat){
				$r[$dia][] = $this->{$cat}[$dia];
			}
		}
		return $r;
	}

	public function __construct($id_establecimiento, $nombre_hoja, array $cats){
		$this->id_establecimiento = $id_establecimiento;
		$this->nombre = $nombre_hoja;
		$this->categorias = array_values($cats);
		foreach($cats as $v){
			$this->{$v} = array();
		}
	}
	public function rangoDias(){
		return range(1, $this->max_dia);
	}
	public function setCategoria($cat){
		if(!in_array($cat, $this->categorias)){
			$this->categorias[] = $cat;
		}
	}
	public function setValorCategoria($cat, $dia, $valor = null){
		$this->max_dia = $dia;
		$this->{$cat}[$dia] = $valor;
	}
	public function setValorIndice($indice, $dia, $valor = null){
		$this->setValorCategoria($this->categorias[$indice], $dia, $valor);
	}
	public function getMes(){
		return self::$meses[strtolower($this->mes)];
	}

	public function getIdUnidad(){
		if(!$this->establecimiento_original){
			$this->error_unidad = "No se reconoce el establecimiento";
			return null;
		}
		try{
			return $this->establecimiento_original->unidadSimilar($this->servicio)->id;
		}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
			$this->error_unidad = "No se reconoce el servicio \"{$this->servicio}\". Seleccione uno de la lista.";
			return null;
		}
	}

	public function getNombreHospital(){
		if($this->establecimiento_original){
			return $this->establecimiento_original->nombre;
		}
		else{
			return null;
		}
	}

	public function __get($name){
		return call_user_func(array($this, "get_{$name}"));
	}
	public function __set($name, $val){
		call_user_func(array($this, "set_{$name}"), $val);
	}

	protected function set_mes($mes){
		$this->mes = $mes;
		$this->max_dia = \Carbon\Carbon::createFromFormat("m", $mes)->lastOfMonth()->day;
		foreach($this->categorias as $v){
			$this->{$v} = array_combine($this->rangoDias(), array_fill(1,$this->max_dia, null));
		}
	}

	protected function set_hospital($hospital){
		$this->hospital = $hospital;
		//$this->comprobar();
	}

	protected function set_servicio($servicio){
		$this->servicio = $servicio;
		//$this->id_servicio = $this->getIdUnidad();
	}

	protected function set_establecimiento_original($obj){
		$this->establecimiento_original = clone $obj;
		$this->id_establecimiento = $obj->id;
	}

	protected function get_mes(){
		return $this->getMes();
	}

	protected function get_hospital(){
		return $this->hospital;
	}

	protected function get_servicio(){
		return $this->id_servicio;
	}

}
class EvolucionController extends Controller{

	protected $hoja_actual;
	protected $datos = array();
	protected $categorias = ['A1', 'A2', 'A3', 'B1', 'B2', 'B3', 'C1', 'C2', 'C3', 'D1', 'D2', 'D3'];
	protected $fila_inicio_datos;
	protected $fin;
	protected $categorias_encontradas = array();
	protected $info_hoja;
	protected $hoja;
	protected $file;
	protected $xls;
	/* @var $xls_export Maatwebsite\Excel\LaravelExcelWriter  */
	protected $xls_export;

	protected $regex_estab = "/^[\t ]*(?:HOSPITAL|ESTABLECIMIENTO)[\t ]*:[\t ]*([A-Za-z0-9 áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ]*[A-Za-z0-9áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ])[\t ]{2,}SERVICIO[\t ]*:[\t ]*([A-Za-z0-9 áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ]*[A-Za-z0-9áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ])[\t ]*$/";
	protected $regex_fecha = "/^[\t ]*[\-A-Za-z0-9 áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ]*[\t ]*:[\t ]*(enero|febrero|marzo|abril|mayo|junio|julio|agosto|sep?tiembre|octubre|noviembre|diciembre)[\t ]*-?\/?[\t ]*([0-9]{4})[\t ]{2,}[A-Za-z0-9 áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ]*[\t ]*:[\t ]*([A-Za-z0-9 áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ]*[A-Za-z0-9áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ])[\t ]*$/i";

	public function buscarPacientesSinCategorizar(Request $request){

		/* $hora = $this->consultarHora();
		$respuesta = "error";
		if(isset($hora->original['exito']) == true ){
			$respuesta = "correcta";
		} */
		$respuesta = "correcta";

		$evoluciones = EvolucionCaso::pacientesSinCategorizar($request->unidad);
		// $especialidades = Especialidades::select("id","nombre")->get();
	return array ($evoluciones, $respuesta/*, $especialidades*/);
	}

	public function subirView(){
		return View::make("Evolucion/Subir");
	}

	public function exportarView(){
		$establecimientos=Establecimiento::getEstablecimientosSinTodos();
		return View::make("Evolucion/Exportar",["establecimientos"=> $establecimientos]);
	}

	protected function encontrarComienzoDeDatos(){
		/*Encuentra la fila donde comienzan los datos (contiene A1).*/
		foreach($this->hoja as $k => $row){
			if(!isset($row[2])) continue;
			if(in_array(trim($row[2]), $this->categorias)){
				$i = 2;
				$titulo_hoja = $this->hoja->getTitle();
				$categorias_encontradas = array();
				while(true){
					$cat = trim($row[$i]);
					if (!in_array($cat, $this->categorias)) break;
					$categorias_encontradas[$i] = $cat;
					$i++;
				}
				$this->infoHoja = new InfoHoja(Session::get("idEstablecimiento"), $titulo_hoja, $categorias_encontradas);
				$this->fila_inicio_datos = $k + 1;
				$this->fin = $i - 1;
				return true;
			}
		}
		return false;
	}

	protected function encontrarMetaDatos(){
		/*Encuentra informacion del hospital, servicio, mes y año y enfermera*/
		$resultados_estab = [];
		$resultados_fecha = [];
		foreach($this->hoja as $k => $row){
			$subject = trim($row[1]);
			if(!preg_match($this->regex_estab, $subject, $resultados_estab)) {
				continue;
			}
			$this->infoHoja->hospital = $resultados_estab[1];
			$this->infoHoja->servicio = $resultados_estab[2];
			$nuevo_subject = trim($this->hoja[$k+1][1]);
			if(!preg_match($this->regex_fecha, $nuevo_subject, $resultados_fecha)){
				break;
			}
			$this->infoHoja->mes = $resultados_fecha[1];
			$this->infoHoja->anno = $resultados_fecha[2];
			$this->infoHoja->enfermera = $resultados_fecha[3];

			return;

		}
	}

	protected function render(){
		return View::make("Evolucion/Tabpanel")->with(array("datos" => $this->datos))->render();
	}

	protected function obtenerDatosCasos($anno, $mes,$idEstablecimiento){
		//$est = Establecimiento::find(Session::get("idEstablecimiento"));
		$est = Establecimiento::find($idEstablecimiento);
		$resultados = $est->evolucionRiesgo($anno, $mes);
		$servicios = array();

		$resultados->each(function($i) use (&$servicios){
			$servicios[$i->unidad_en_establecimiento][] = $i;


		});



		foreach($servicios as $id_servicio => $objs){
			$unidad = UnidadEnEstablecimiento::find($id_servicio);
			$hoja = new InfoHoja($idEstablecimiento, $unidad->alias, $this->categorias);
			$hoja->hospital = $est->nombre;
			$hoja->servicio = $unidad->alias;
			$hoja->anno = $anno;
			$hoja->mes = $mes;
			$usuario = Auth::user();
			$hoja->enfermera = "{$usuario->nombres} {$usuario->apellido_paterno} {$usuario->apellido_materno}";
			foreach($objs as $obj){
				if(is_null($obj->riesgo)) continue;
				$fecha = \Carbon\Carbon::createFromFormat("Y-m-d", $obj->fecha);
				$hoja->{$obj->riesgo}[$fecha->day] = (int) $obj->cantidad;
			}
			$this->datos[$unidad->alias] = $hoja;
		}
	}

	public function cargar(Request $request){
		DB::connection()->enableQueryLog();
		$fecha = \Carbon\Carbon::createFromFormat("m-Y", $request->input("fecha"));
		$idEstablecimiento = $request->input("establecimiento");
		//return $request->input("establecimiento");
		$anno = $fecha->year;
		$mes = $fecha->month;
		$this->obtenerDatosCasos($anno, $mes, $idEstablecimiento);
		Session::put("xls_anno", $anno);
		Session::put("xls_mes", $mes);
		Session::put("datos_xls", $this->datos);
		Session::put("temporalEstablecimiento",$idEstablecimiento);
		$count = count($this->datos);
		$exito = ($count == 0) ? false : true;
		$msg = ($exito) ? "" : "No se han encontrado datos para la fecha ingresada.";
		$queries = DB::getQueryLog();

		//Debugbar::info($queries);



		return response()->json(["out" => $exito, "contenido" => $this->render(), "msg" => $msg]);
	}




	public function generar(){

		$this->datos = Session::get("datos_xls");
		$anno = Session::get("xls_anno");
		$mes = Session::get("xls_mes");
		$mes = InfoHoja::$meses[$mes];
		$path = storage_path();
		$id_est = Session::get("temporalEstablecimiento");
		$this->usuario = Auth::user();
		$this->establecimiento = Establecimiento::find($id_est);


		Config::set(['excel.export.calculate' => true]);
		Excel::load("storage/data/plantilla/plantilla.xlsx", function($file)
		{
			$sheet1 = $file->setActiveSheetIndex(1);
			$file->calculate();


			Excel::create('Categorizacion', function($excel) use ($sheet1) {

				$excel->addExternalSheet($sheet1);

				$i=0;
				foreach($this->datos as $this->nombre => $this->dato_actual){
					$sc =   $excel->getActiveSheet(0)->copy();

					$sc->setCellValue("C6", $this->establecimiento->nombre);
					$sc->fromArray($this->dato_actual->array_dias(), null, "B11");



					$sc->setTitle(preg_replace("/[^ A-Za-z0-9áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ:\-_]/", "-", substr($this->nombre,0,20))." ".substr($this->dato_actual->mes, 0, 3)." {$this->dato_actual->anno}");

					$sc->setCellValue("C6", $this->establecimiento->nombre);
					$sc->setCellValue("C7", "{$this->dato_actual->mes} {$this->dato_actual->anno}");
					$sc->setCellValue("K6", $this->nombre);
					$sc->setCellValue("K7", "{$this->usuario->nombres} {$this->usuario->apellido_paterno} {$this->usuario->apellido_materno}");



					$i++;
					$excel->addSheet($sc,$i);


				}


				$excel->removeSheetByIndex(0);





			})->export('xlsx');
		});




		/*Excel::create('workc', function($excel) {
			$path = storage_path();
			$excelDatos =   Excel::load("{$path}/data/plantilla/plantilla.xlsx");
			$sheet1 = $excelDatos->setActiveSheetIndex(1);
			//dd($excelDatos->getActiveSheet());
			//for($i=0;$i<3;$i++){
				$excel->addExternalSheet($sheet1);

				$sheet1 = $sheet1->setTitle("a");
				$excel->addExternalSheet($sheet1);


			//}

		})->download('xlsx');
		*/

	}





	protected function generar_hoja(){


		$hoja = clone $this->plantilla->getActiveSheet();
		$hoja->setTitle(preg_replace("/[^ A-Za-z0-9áéíóúÄËÏÖÜàèìòùçàèìòùñÁÉÍÓÚäëïöüÀÈÌÒÙÇÑ:\-_]/", "-", substr($this->nombre,0,20))." ".substr($this->dato_actual->mes, 0, 3)." {$this->dato_actual->anno}");
		$hoja->setCellValue("C6", $this->establecimiento->nombre);
		$hoja->setCellValue("C7", "{$this->dato_actual->mes} {$this->dato_actual->anno}");
		$hoja->setCellValue("K6", $this->nombre);
		$hoja->setCellValue("K7", "{$this->usuario->nombres} {$this->usuario->apellido_paterno} {$this->usuario->apellido_materno}");
		$hoja->fromArray($this->dato_actual->array_dias(), null, "B11");

		//dd($this->xls_export->excel);
		$this->xls_export->excel->addExternalSheet($hoja);


	}

	public function consultarHora(){

		$hr_actual = Carbon::now();
		$hr = $hr_actual->format("H");
		$t = (string) $hr;
		/* if($hr > 0 && $hr < 18){
			return response()->json(["exito" => $hr]);
		}else{
			return response()->json(["error" => $hr,]);
		} */

		if($t === '00'){
			return response()->json(["exito" => $hr]);
		}else

		if($hr >= 0 && $hr < 18){
			return response()->json(["exito" => $hr]);
		}else{
			return response()->json(["error" => $hr]);
		}
	}
}
