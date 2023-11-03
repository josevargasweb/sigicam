<?php
use Carbon\Carbon;
namespace App\Http\Controllers;

use App\Models\Establecimiento;
use View;
use Carbon\Carbon;

class EstadisticasCamasHabilitadasController extends EstadisticasController{

	public function pagina(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		return View::make("Estadisticas/CamasHabilitadas", ["establecimiento" => $establecimiento,
			"fecha_desde" => Carbon::now()->subDay()->format('d-m-Y'),
			"fecha_hasta" => Carbon::now()->format('d-m-Y') ]);
	}

	public function reporte(){
		/*$this->establecimiento = $establecimiento;
		$this->fecha = $fecha;
		$e = new EstadisticaCamasHabilitadasEstablecimiento($this->establecimiento, $this->fecha);*/
		$prom_desuso = array();
		foreach($this->estadisticaHabilitadas->promedioDesuso() as $prom){
			$prom_desuso[$prom->nombre_unidad] = is_null($prom->promedio)? 'No hay desuso': $prom->promedio;
		}
		$tiempo_desuso = $this->estadisticaHabilitadas->ultimoDesuso();
		//var_dump($tiempo_desuso);
		array_walk( $tiempo_desuso, function($it){
			$it->desocupacion = $it->desocupacion? $it->desocupacion : "Nunca ocupado";
		});
		//$tiempo_desuso = $tiempo_desuso
		$g = new GraficoRanking( $this->estadisticaHabilitadas );		
		$g->grafico->setTitulo($this->titulo);
		$g->grafico->esconderSeries();
		$especialidades = array();
		foreach($this->estadisticaHabilitadas->getCategorias() as $esp){
			$especialidades[] = $esp->categoria;
		}

		return json_encode(array(
			"t_camas" => $tiempo_desuso,
			"promedios" => $prom_desuso,
			"g_camas" => json_decode($g->get()),
			"especialidades" => $especialidades,
			"titulos" => $this->titulos,
		));
	}

	public function update(){
		
	}

	public function reporteTotal($fecha_desde, $fecha_hasta){
		$this->titulo = "Camas habilitadas por establecimiento";
		$this->estadisticaHabilitadas = new EstadisticaCamasHabilitadas($fecha_desde, $fecha_hasta);
		$this->titulos = ['Establecimiento', 'Servicio'];
		return $this->reporte();
	}

	public function reporteEstablecimiento($fecha_desde, $fecha_hasta, $establecimiento){
		$this->titulo = "Camas habilitadas por servicio";
		$this->estadisticaHabilitadas = new EstadisticaCamasHabilitadasEstablecimiento($establecimiento, $fecha_desde, $fecha_hasta);
		$this->titulos = ['Servicio', 'Sala'];
		return $this->reporte();

	}

	public function reporteUnidad($fecha, $establecimiento, $unidad){
		
	}


}