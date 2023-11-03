<?php namespace App\Models{

use \Ghunti\HighchartsPHP\Highchart;

abstract class Grafico {

	protected $tooltip = "{point.name}<br>Valor: <b>{point.y}</b>";

	public function __construct(){
		$this->chart = new Highchart();
		$this->chart->series = array();
		$this->chart->chart = array(
			'plotBackgroundColor' => null,
			'plotBorderWidth' => 1,
			'plotShadow' => false,
			 "zoomType"=>'xy'
		);
		$this->chart->yAxis->allowDecimals =false;
		$this->chart->tooltip = array("pointFormat" => $this->tooltip);
		$this->set();
	}

	public abstract function set();
	public function setTitulo($titulo){
		$this->chart->title = array("text" => $titulo);
		return $this;
	}
	public function renderJson(){
		return $this->chart->renderOptions();
	}
	public function agregarSerie($datos, $nombre = ''){
		//echo "Serie:";
		//var_dump($datos);
		$this->chart->series[] = array("type" => $this->tipo, "name" => $nombre, "data" => $datos);
		return $this;
	}
	public function esconderSeries(){
		$this->chart->plotOptions->series['showInLegend'] = false;
		return $this;
	}
}

class GraficoCircular extends Grafico{
	public function __construct(){
		$this->pcategorias = array();
		parent::__construct();
	}
	protected $tipo = "pie";
	protected $tooltip = "{point.name}<br>Valor: <b>{point.y}</b> Porcentaje: <b>{point.percentage:.1f}%</b>";	
	public function set(){
		$this->chart->plotOptions->pie = array(
			'allowPointSelect'=> true,
			'cursor' => 'pointer',
			'dataLabels' => array(
				"enabled" => true,
				"format" => '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)',
				"style" => array(
					"color" => "(Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'"
				)
			)
		);

	}
	public function setCategorias($cats){	
		$this->pcategorias = array();
		foreach($cats as $cat){
			$this->pcategorias[] = $cat;
		}
		//var_dump($this);
		return $this;
	}

	public function agregarSerie($datos, $nombre = ''){
		foreach($this->pcategorias as $k => $cat){
			$datos[$k] = [$cat, $datos[$k]];
		}
		parent::agregarSerie($datos, $nombre);
		return $this;
	}
}

abstract class GraficoBarras extends Grafico{
	public function setCategorias($cats){
		$this->chart->xAxis['categories'] =  $cats ;
		return $this;
	}
	public function setYMin($min){
		$this->chart->yAxis->min = $min;
		return $this;
	}
	public function setTituloY($titulo){
		$this->chart->yAxis->title = array("text" => $titulo);
		return $this;
	}
		
}

class GraficoApilado extends GraficoBarras{
	protected $tipo = "bar";

	public function set(){
		$this->chart->plotOptions->series = array("stacking" => "normal");
		$this->chart->legend = array("reversed" => true);
		//$this->chart->xAxis = array( "labels" => array("rotation" => -45), "categories" => array() );
	}
}

class GraficoColumnas extends GraficoBarras{
	protected $tipo = "column";

	public function set(){
		$this->chart->xAxis->min =0;
		$this->chart->tooltip = array(
			"headerFormat" => '<span style="font-size:10px">{point.key}</span><table>',
			"pointFormat" => '<tr><td style="color:{series.color};padding:0">{series.name}: </td><td style="padding:0"><b>{point.y}</b></td></tr>',
			"footedFormat" => '</table>',
			"shared" => true,
			"useHTML" => true,"pointFormat" => $this->tooltip
		);
		$this->chart->plotOptions = array("column" => array( "pointPadding" => 0.2, "borderWidth" => 0 ) );
		$form = "function() {var words = this.name.split(/[\s]+/);var numWordsPerLine = 3;var str = [];for (var word in words) {
        if (word > 0 && word % numWordsPerLine == 0) str.push('<br>'); str.push(words[word]); } return str.join(' ');}";
		$this->chart->xAxis = array( "labels" => array("rotation" => -45, "style" => ["width"=> "150px", "min-width" => "150px", "fontSize" => "8px", "align" => "center", "formatter" => $form]), "categories" => array() );
			
		$this->chart->plotOptions->column = array(
			'dataLabels' => array(
			"enabled" => true,	
			)
		);

	}
}

}

