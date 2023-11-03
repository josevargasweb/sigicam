<?php
use \Ghunti\HighchartsPHP\Highchart;
namespace App\Http\Controllers;


use \Ghunti\HighchartsPHP\Highchart;

use App\Models\Establecimiento;
use View;
use DB;

class EstadisticasRiesgoController extends Controller{


	public function pagina(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");

		return View::make("Estadisticas/ReporteRiesgo", ["establecimiento" => $establecimiento, "fecha" => $fecha]);
	}

	public function reporte($fecha_inicio,$fecha,$est=null){

		$especialidades = DB::table("casos")
		->join("t_evolucion_casos AS tec", "tec.caso", "=", "casos.id")
		->join("historial_ocupaciones_vista as t","t.caso","casos.id")//esto indica que el paciente al menos estuvo hospitalizado
		->join("establecimientos", "casos.establecimiento","=","establecimientos.id")
		
		//->select("alias as categoria")->distinct()
		->select("tec.riesgo as alias",DB::raw("count(*)"))
		->whereRaw("establecimientos.id = ".$est )
		->whereRaw("tec.fecha >= '".$fecha_inicio."' AND tec.fecha <= '".$fecha."'")
		->whereRaw("(tec.fecha <= t.fecha_liberacion or t.fecha_liberacion is null)")//no hay que considerar a los pacientes con egreso de hospitalizacion domiciliaria
		->whereNotNull('tec.riesgo')
		
		->groupBy("tec.riesgo")
		->get();

		//->orderBy("categoria", "asc")->get();

		return json_encode(array("especialidades"=>$especialidades, "fechainicio"=>$fecha));
	}


	public function reporteAdminSS($fecha_inicio,$fecha){

		$especialidades = DB::table("establecimientos")
		->join("unidades_en_establecimientos AS unidades","unidades.establecimiento", "=", "establecimientos.id")
		->join("salas AS s", "s.establecimiento", "=", "unidades.id")
		->join("camas AS cm", "cm.sala", "=", "s.id")
		->join("historial_ocupaciones AS ho","ho.cama","=","cm.id")
		//->select("alias as categoria")->distinct()
		->select("establecimientos.nombre AS alias",DB::raw("count(*)"))
		//->whereRaw("unidades.establecimiento = 8" )
		->whereRaw("s.id IS NOT NULL")
		->whereRaw("cm.id IS NOT NULL")
		->whereRaw("(motivo ='alta' OR motivo = 'fallecimiento' OR motivo='traslado externo' OR motivo='otro' OR motivo='traslado extra sistema' OR motivo = 'hospitalización domiciliaria' )")
		->whereRaw("fecha_liberacion >= '".$fecha_inicio."' AND fecha_liberacion <= '".$fecha."'")
		->groupBy("establecimientos.id")
		->get();



			return json_encode(array("especialidades"=>$especialidades));
	}


	public function update(){

	}

/*	public function reporteEstablecimiento($fecha_desde, $fecha_hasta, $establecimiento){
	/*	$this->titulo = "Camas habilitadas por servicio";
		$this->estadisticaHabilitadas = new EstadisticaCamasHabilitadasEstablecimiento($establecimiento, $fecha_desde, $fecha_hasta);
		$this->titulos = ['Servicio', 'Sala'];
		return $this->reporte();
		return "no funcionando";

	}*/
}
