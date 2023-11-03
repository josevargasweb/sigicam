<?php
namespace App\Http\Controllers;
use View;
use DB;

class EstadisticasCasoSocialController extends Controller{

	/* @var $estadisticaDerivaciones EstadisticaDerivaciones */
	//protected $estadisticaCasoSocial;

	public function pagina(){
		$establecimiento=\App\Models\Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		return View::make("Estadisticas/ReporteCasoSocial", ["establecimiento" => $establecimiento, "fecha" => $fecha]);
	}

	public function reporte($fecha_inicio,$fecha,$est=null){

		$casoSocial=new \App\Models\EstadisticaCasoSocial($fecha_inicio,$fecha);
		if($est)
			$casoSocial->setEstablecimiento($est);
		$casos=$casoSocial->totalCasos();

		$g = new \App\Models\GraficoCircular();
		$g->setTitulo("Reporte de casos sociales");

		$g->setCategorias(["Caso social cerrado","No caso social","Caso social abierto"]);

		$g->agregarSerie($casos);

		$sqlest = "";
		if($est)
		{
			$sqlest=" AND establecimiento=".$est;
		}

		$tablaCasoSocial = DB::select(DB::raw("SELECT rut,dv,(fecha_termino IS NULL) AS caso_social,pacientes.nombre AS nombre_paciente,fecha_nacimiento,apellido_paterno,apellido_materno, establecimientos.nombre FROM casos JOIN
				pacientes ON pacientes.id = casos.paciente
			JOIN establecimientos ON establecimientos.id = casos.establecimiento WHERE  (caso_social=true AND fecha_termino is null AND casos.updated_at>='".$fecha_inicio."' AND casos.updated_at<='".$fecha."' $sqlest) or (casos.updated_at>='".$fecha_inicio."' AND fecha_termino is not null AND casos.updated_at<='".$fecha."' AND caso_social=true $sqlest) "));

		return json_encode(
			array(
				"g_caso_social" => json_decode($g->renderJson()),
				"tabla_caso_social" => $tablaCasoSocial
				)
		);

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
