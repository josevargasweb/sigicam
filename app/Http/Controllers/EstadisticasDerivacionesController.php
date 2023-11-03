<?php
use \Ghunti\HighchartsPHP\Highchart;
namespace App\Http\Controllers;


use \Ghunti\HighchartsPHP\Highchart;

use App\Models\Establecimiento;
use View;
use DB;


class EstadisticasDerivacionesController extends Controller{

	/* @var $estadisticaDerivaciones EstadisticaDerivaciones */
	protected $estadisticaDerivaciones;

	public function pagina(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		return View::make("Estadisticas/ReporteDerivaciones", ["establecimiento" => $establecimiento, "fecha" => $fecha]);
	}

	public function reporte($fecha_inicio, $fecha){

		$this->derivaciones_aceptadas = new \App\Models\DerivacionesAceptadasEstablecimiento($fecha_inicio, $fecha);

		$this->derivaciones_rechazadas = new \App\Models\DerivacionesRechazadasEstablecimiento($fecha_inicio, $fecha);

		$this->derivaciones_promedio = new \App\Models\DerivacionesDemoraEstablecimiento($fecha_inicio, $fecha);


		$vars = array(
			"id_est" => $this->id_est,
			"g_est_aceptadas" => $this->graficoAceptadasEstablecimientos(),
			"g_est_rechazadas" => $this->graficoRechazadasEstablecimientos(),
			"g_est_promedio" => $this->graficoPromediosEstablecimientos(),
			"g_riesgo" => $this->graficoCategorias() ,
			"g_general"	 => $this->graficoEstadoDerivaciones(),

			"g_mensual"	 => $this->graficoUnidad(),
			"promedio_e" => $this->estadisticaDerivaciones->promedioAceptacionEnviados(),
			"enviadas_aceptadas" => $this->estadisticaDerivaciones->enviadas_aceptadas(),
			"enviadas_aceptadas_pendiente" => $this->estadisticaDerivaciones->enviadas_aceptadas_pendiente(),
			"enviadas_canceladas" => $this->estadisticaDerivaciones->enviadas_canceladas(),
			"enviadas_rechazadas" => $this->estadisticaDerivaciones->enviadas_rechazadas(),
			"enviadas_en_espera" => $this->estadisticaDerivaciones->enviadas_en_espera(),
			"enviadas_vs_aceptadas" => $this->graficoDerivadasVsAceptadas($fecha_inicio, $fecha),
		);
		//return "OK1";
		if($this->id_est) {
			$vars_rec = array(

			"g_riesgo_recibidas" => ( $this->graficoCategoriasRecibidas() ),
			"promedio_r" => $this->estadisticaDerivaciones->promedioAceptacionRecibidos(),
			"g_general_recibidas" => ( $this->graficoEstadoDerivacionesRecibidas() ),
			"recibidas_aceptadas" => $this->estadisticaDerivaciones->recibidas_aceptadas(),
			"recibidas_aceptadas_pendiente" => $this->estadisticaDerivaciones->recibidas_aceptadas_pendiente(),
			"recibidas_canceladas" => $this->estadisticaDerivaciones->recibidas_canceladas(),
			"recibidas_rechazadas" => $this->estadisticaDerivaciones->recibidas_rechazadas(),
			"recibidas_en_espera" => $this->estadisticaDerivaciones->recibidas_en_espera(),
			"enviadas_vs_aceptadas" => $this->graficoDerivadasVsAceptadas($fecha_inicio, $fecha),

			);
		}
		else $vars_rec = array();

		$contenido = View::make("Estadisticas/ContenidoDerivaciones", $vars + $vars_rec)->render();
		return response()->json(["contenido" => $contenido]);

	}

	public function graficoDerivadasVsAceptadas($fecha_inicio, $fecha){
		$chart = new Highchart();
		$chart->title = array("text" => "Derivadas vs aceptadas");
		$chart->plotOptions = array("column"=>array("dataLabels"=>array("enabled"=>true)));


		$form = "function() {var words = this.name.split(/[\s]+/);var numWordsPerLine = 3;var str = [];for (var word in words) {
        if (word > 0 && word % numWordsPerLine == 0) str.push('<br>'); str.push(words[word]); } return str.join(' ');}";

        //$establecimientos = Establecimiento::getEstablecimientosSinTodos()->orderBy("nombre");
      	$establecimientos = Establecimiento::orderBy("nombre")->get();

      	$estab = array();
      	$derivadas = array();
      	$aceptadas = array();
      	/*
      	Derivaciones recibidas !!!!
      	 */
      	foreach ($establecimientos as $value) {
      		$cantDerivadas = \App\Models\Derivacion::select(DB::raw("count(*) as cont"))
      		->join("unidades_en_establecimientos AS ue","ue.id","=","derivaciones.destino")
      		->where("ue.establecimiento","=",$value->id)
      		->whereRaw("motivo_cierre <> 'cancelado'")
      		//->whereRaw("fecha >= '$fecha_inicio' AND fecha <= '$fecha'")
      		->first();

      		$cantAceptadas = \App\Models\Derivacion::select(DB::raw("count(*) as cont"))
      		->join("unidades_en_establecimientos AS ue","ue.id","=","derivaciones.destino")
      		->where("ue.establecimiento","=",$value->id)
      		->whereRaw("(motivo_cierre = 'aceptado' or motivo_cierre='aceptado, pendiente de cama')")
      		//->whereRaw("fecha_cierre >= '$fecha_inicio' AND fecha_cierre <= '$fecha'")
      		->first();

      		array_push($estab, $value->nombre);
      		array_push($derivadas, $cantDerivadas->cont);
      		array_push($aceptadas, $cantAceptadas->cont);

      	}


		$chart->xAxis = array( "labels" => array("rotation" => -45, "style" => ["width"=> "150px", "min-width" => "150px", "fontSize" => "8px", "align" => "center", "formatter" => $form]), "categories" => $estab );

		$chart->series[0]->type = "column";
		$chart->series[0]->name = "Derivaciones";
		$chart->series[0]->data = $derivadas;

		$chart->series[1]->type = "column";
		$chart->series[1]->name = "Aceptadas";
		$chart->series[1]->data = $aceptadas;





		return $chart->renderOptions();


		/*$chart = new Highchart();
		$chart->title = array('text' => 'Monthly Average Temperature', 'x' => -20);
		$chart->series[] = array('name' => 'Tokyo', 'data' => array(7.0, 6.9, 9.5));
		$chart->series[0] = array('name' => 'Tokyo', 'data' => array(7.0, 6.9, 9.5));
		return $chart->renderOptions();
		*/
	}

	public function graficoAceptadasEstablecimientos(){
		$e = new \App\Models\GraficoPorUnidad($this->derivaciones_aceptadas);
		return $e->setTitulo("Total de derivaciones aceptadas por establecimiento receptor")
			->setTituloY("Cantidad")->setYMin(0)->get();
	}

	public function graficoRechazadasEstablecimientos(){
		$e = new \App\Models\GraficoPorUnidad($this->derivaciones_rechazadas);
		return $e->setTitulo("Total de derivaciones rechazadas por establecimiento receptor")
			->setTituloY("Cantidad")->setYMin(0)->get();
	}

	public function graficoPromediosEstablecimientos(){

		$e = new \App\Models\GraficoPorUnidad($this->derivaciones_promedio);
		return $e->setTitulo("Promedio de aceptación de derivaciones por establecimiento receptor")
			->setTituloY("Días")->setYMin(0)->get();
	}


	public function graficoEstadoDerivaciones(){
		$this->graficoDerivaciones = new \App\Models\GraficoCircular();
		return $this->graficoDerivaciones->setTitulo("Estado general de solicitudes de derivación enviadas")
		->agregarSerie( array(
			array("aceptadas", $this->estadisticaDerivaciones->enviadas_aceptadas() ),
			array("aceptadas, cama pendiente", $this->estadisticaDerivaciones->enviadas_aceptadas_pendiente() ),
			array("en espera"	, $this->estadisticaDerivaciones->enviadas_en_espera() ),
			array("rechazadas", $this->estadisticaDerivaciones->enviadas_rechazadas() ),
			array("canceladas", $this->estadisticaDerivaciones->enviadas_canceladas() ),
		), "dotacion")->renderJson();
	}

	public function graficoEstadoDerivacionesRecibidas(){
		$this->graficoDerivaciones = new \App\Models\GraficoCircular();
		return $this->graficoDerivaciones->setTitulo("Estado general de solicitudes de derivación recibidas")
			->agregarSerie( array(
				array("aceptadas", $this->estadisticaDerivaciones->recibidas_aceptadas() ),
				array("aceptadas, cama pendiente", $this->estadisticaDerivaciones->recibidas_aceptadas_pendiente() ),
				array("en espera"	, $this->estadisticaDerivaciones->recibidas_en_espera() ),
				array("rechazadas", $this->estadisticaDerivaciones->recibidas_rechazadas() ),
				array("canceladas", $this->estadisticaDerivaciones->recibidas_canceladas() ),
			), "dotacion")->renderJson();
	}

	public function graficoUnidad(){
		$e = new \App\Models\GraficoPorUnidad($this->derivacionesMensuales);
		return $e->setTitulo("Total de derivaciones aceptadas mensuales, últimos año")
		->setTituloY("Cantidad")->setYMin(0)->get();
	}

	public function graficoCategorias(){
		//return print_r($this->derivacionesCategorias);
		$e = new \App\Models\GraficoPorUnidad($this->derivacionesCategorias);

		$e->grafico->setTitulo("Derivaciones enviadas por categoría de riesgo en el momento de derivar");
		return $e->get();
	}

	public function graficoCategoriasRecibidas(){
		$e = new \App\Models\GraficoPorUnidad($this->derivacionesRecibidasCategorias);
		$e->grafico->setTitulo("Derivaciones recibidas por categoría de riesgo en el momento de derivar");
		return $e->get();
	}

	public function reporteTotal($fecha_inicio, $fecha){
		//return "";
		$this->id_est = false;
		$this->estadisticaDerivaciones = new \App\Models\EstadisticaDerivacionesTotal($fecha_inicio, $fecha);
		$this->derivacionesMensuales = new \App\Models\DerivacionesMensualesTotal($fecha_inicio, $fecha);
		$this->derivacionesCategorias = new \App\Models\RankingDerivacionesCategoriasTotal($fecha_inicio, $fecha);
		$this->derivacionesRecibidasCategorias = new \App\Models\RankingDerivacionesRecibidasCategoriasTotal($fecha_inicio, $fecha);
		return $this->reporte($fecha_inicio, $fecha);
	}

	public function reporteEstablecimiento($fecha_inicio, $fecha, $establecimiento){
		$this->id_est = true;
		$this->estadisticaDerivaciones = new \App\Models\EstadisticaDerivaciones($establecimiento, $fecha_inicio, $fecha);
		$this->derivacionesMensuales = new \App\Models\DerivacionesMensuales($establecimiento, $fecha_inicio, $fecha);

		$this->derivacionesCategorias = new \App\Models\RankingDerivacionesCategorias($establecimiento, $fecha_inicio, $fecha);
		$this->derivacionesRecibidasCategorias = new \App\Models\RankingDerivacionesRecibidasCategorias($establecimiento, $fecha_inicio, $fecha);
		return $this->reporte($fecha_inicio, $fecha);
	}

	public function reporteUnidad($fecha_inicio, $fecha, $establecimiento, $unidad){
		$this->id_est = true;
		$this->estadisticaDerivaciones = new EstadisticaDerivacionesUnidad($establecimiento, $unidad, $fecha_inicio, $fecha);
		$this->derivacionesMensuales = new DerivacionesMensualesUnidad($establecimiento, $unidad, $fecha_inicio, $fecha);
		$this->derivacionesCategorias = new RankingDerivacionesCategoriasUnidad($establecimiento, $unidad, $fecha_inicio, $fecha);

		return $this->reporte($fecha_inicio, $fecha);
	}

}
