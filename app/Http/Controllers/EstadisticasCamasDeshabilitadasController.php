<?php
use Carbon\Carbon;
namespace App\Http\Controllers;

use App\Models\Establecimiento;
use View;
use Carbon\Carbon;

class EstadisticasCamasDeshabilitadasController extends EstadisticasController{

	public function pagina(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		return View::make("Estadisticas/CamasDeshabilitadas", ["establecimiento" => $establecimiento,
			"fecha_desde" => Carbon::now()->subDay()->format('d-m-Y'),
			"fecha_hasta" => Carbon::now()->format('d-m-Y') ]);
	}

	public function reporte(){
		//$e = new EstadisticaCamasDeshabilitadasEstablecimiento($establecimiento, $fecha);
		$promedios = array();
		foreach($this->estadistica->promediosDeshabilitadas() as $prom){
			$promedios[$prom->nombre_unidad] = is_null($prom->promedio)?'No hay inhabilitadas':$prom->promedio;
		}
		$tiempo = $this->estadistica->camasDeshabilitadas();
		$g = new GraficoRanking( $this->estadistica );		
		$g->grafico->setTitulo($this->titulo);
		$g->grafico->esconderSeries();
		$especialidades = array();
		foreach($this->estadistica->getCategorias() as $esp){
			$especialidades[] = $esp->categoria;
		}

		return json_encode(array(
			"t_camas" => $tiempo,
			"promedios" => $promedios,
			"g_camas" => json_decode($g->get()),
			"especialidades" => $especialidades,
			"titulos" => $this->titulos,
		));
	}

	public function reporteTotal($fecha_desde, $fecha_hasta){
		$this->titulo = "Camas inhabilitadas por establecimiento";
		$this->estadistica = new EstadisticaCamasDeshabilitadas($fecha_desde, $fecha_hasta);
		$this->titulos = ['Establecimiento', 'Servicio', 'Sala', 'Cantidad de camas inhabilitadas'];
		return $this->reporte();
	}

	public function reporteEstablecimiento($fecha_desde, $fecha_hasta, $establecimiento){
		$this->titulo = "Camas inhabilitadas por servicio";
		$this->estadistica = new EstadisticaCamasDeshabilitadasEstablecimiento($establecimiento, $fecha_desde, $fecha_hasta);
		$this->titulos = ['Servicio', 'Sala', 'Cama', 'Tiempo inhabilitada (días)'];
		return $this->reporte();

	}

	public function reporteUnidad($fecha, $establecimiento, $unidad){
		
	}
}