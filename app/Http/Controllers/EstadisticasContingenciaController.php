<?php
namespace App\Http\Controllers;
class EstadisticasContingenciaController extends Controller{

	/* @var $estadisticaDerivaciones EstadisticaDerivaciones */
	//protected $estadisticaCasoSocial;

	public function pagina(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		return View::make("Estadisticas/ReporteContingencia", ["establecimiento" => $establecimiento, "fecha" => $fecha]);
	}
	
	public function reporte($fecha_inicio,$fecha,$est=null){


			
			

		$solicitudes = DB::table("solicitudes_contingencia")
		->select("establecimientos.nombre AS nombre",DB::raw("count(*)"))
		->join("establecimientos", "establecimientos.id","=","solicitudes_contingencia.establecimiento")
		->where("establecimientos.id","=",$est)
		->groupBy("establecimientos.nombre")
		->get();


			return json_encode(array("solicitudes"=>$solicitudes));
	}


	public function reporteAdminSS($fecha_inicio,$fecha){

		$solicitudes = DB::table("solicitudes_contingencia")
		->select("establecimientos.nombre AS nombre",DB::raw("count(*)"))
		->join("establecimientos", "establecimientos.id","=","solicitudes_contingencia.establecimiento")
		->groupBy("establecimientos.nombre")
		->get();



			return json_encode(array("solicitudes"=>$solicitudes));
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

