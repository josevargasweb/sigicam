<?php
namespace App\Http\Controllers;

use App\Models\Establecimiento;
use View;
use Carbon\Carbon;
use App\Models\EstadisticaCVC;

class EstadisticasIAASController extends Controller{

	protected $data = array();


	public function expIAAS(){
		return View::make("Estadisticas/ExportarIAAS");
	}

	public function generar(){
		$this->data = Session::get("datos_xls");
		$id_est = Session::get("idEstablecimiento");
		if($id_est!=null)$this->establecimiento = Establecimiento::find($id_est);

		$this->usuario = Auth::user();
		$path = storage_path();
		$this->plantilla = Excel::selectSheets("Categorización")->load("{$path}/data/plantilla/iaas.xlsx");
		$this->plantilla->calculate();

		$this->xls_export = Excel::create("IAAS", function($excel){
			$excel->setTitle("HOJA REGISTRO MENSUAL CATEGORIZACIÓN DE USUARIOS DEPENDENCIA Y RIESGO");
			$excel->setCreator("{$this->usuario->nombres} {$this->usuario->apellido_paterno} {$this->usuario->apellido_materno}");
			if(Session::get("idEstablecimiento")!=null)$excel->setCompany("{$this->establecimiento->nombre} - SSVQ");
		});
		PHPExcel_Calculation::getInstance()->flushInstance();
			$this->generar_hoja();
		return $this->xls_export->export("xls");
	}	
	
	protected function generar_hoja(){
		$hoja = clone $this->plantilla->excel->getActiveSheet();
		if(Session::get("idEstablecimiento")!=null)$hoja->setCellValue("C7", $this->establecimiento->nombre);
		else $hoja->setCellValue("C7","ADMINISTRADOR");
		$hoja->setCellValue("C8", date("m-Y") );
		$hoja->setCellValue("K8", "{$this->usuario->nombres} {$this->usuario->apellido_paterno} {$this->usuario->apellido_materno}");
		$hoja->fromArray($this->data, null, "A12");

		$this->xls_export->excel->addExternalSheet($hoja);
	}	
	
	
	public function MostrarListaIAAS(){
		$establecimiento=Session::get('idEstablecimiento');
		$sqlest="";
		if(!empty($establecimiento))$sqlest="AND c.establecimiento=$establecimiento";

		$data=EstadisticaInfecciones::obtenerListaIAAS($sqlest);
		Session::flash("datos_xls", $data);

			return Response::json(["aaData" => $data]);
	}


	public function MostrarIAASFecha(){

		$fecha2=Input::get("fecha");

		$datas=EstadisticaInfecciones::obtenerListaFechaIAAS($fecha2);
		$this->data=$datas;
		Session::flash("datos_xls", $datas);

		$count = count($datas);
		$exito = ($count == 0) ? false : true;
		$msg = ($exito) ? "" : "No se han encontrado datos para la fecha ingresada.";

		return Response::json(["out" => $exito, "msg" => $msg,"contenido"=>$fecha2]);
		
	}	

	public function pagina(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		return View::make("Estadisticas/ReporteIAAS", ["establecimiento" => $establecimiento, "fecha" => $fecha]);
	}
	
	public function reporte($fecha_inicio,$fecha,$est=null){

		//cvc
		$g=$this->graficoCVC($fecha_inicio,$fecha,$est);

		//cvc revolution
		$gReload=$this->graficoNewCVC($fecha_inicio,$fecha,$est);

		//infecciones
		$ginf=$this->graficoInfecciones($fecha_inicio,$fecha,$est);
		//localizacion
		$gloc=$this->graficoLocalizacion($fecha_inicio,$fecha,$est);
		//Clostridium
		$gclos=$this->graficoClostridium($fecha_inicio,$fecha,$est);

		//Tracto urinario
		$gUrinario=$this->graficoUrinario($fecha_inicio,$fecha,$est);
		

		return json_encode(
			array(
				"g_cvc" => json_decode($g->renderJson()),
				"g_infecciones" => json_decode($ginf->renderJson()),
				"g_localizacion" => json_decode($gloc->renderJson()),
				"g_clostridium" => json_decode($gclos->renderJson()),
				"g_ReloadCvc" => json_decode($gReload->renderJson()),
				"gUrinario" => json_decode($gUrinario->renderJson()),
				)
		);
			
	}

	public function update(){
		
	}
	private function graficoCVC($fecha_inicio,$fecha,$est=null)
	{
		$cvc=new EstadisticaCVC($fecha_inicio,$fecha);
		if($est)
			$cvc->setEstablecimiento($est);
		$resultado=$cvc->total();

		$g = new GraficoColumnas();		
		$g->setTitulo("Reporte de CVC");

		$g->setCategorias($cvc->categorias());

		$g->agregarSerie($resultado,"ITS asociadas a CVC");
		$g->setTituloY("Cantidad");
		return $g;
	}
	private function graficoInfecciones($fecha_inicio,$fecha,$est=null)
	{
		$inf=new EstadisticaInfecciones($fecha_inicio,$fecha);
		if($est)
			$inf->setEstablecimiento($est);
		$resultado=$inf->total();

		$g = new GraficoColumnas();		
		$g->setTitulo("Reporte de Infecciones");

		$g->setCategorias($inf->categorias());

		$g->agregarSerie($resultado,"Infecciones");
		$g->setTituloY("Cantidad");
		return $g;
	}
	private function graficoLocalizacion($fecha_inicio,$fecha,$est=null)
	{
		$loc=new EstadisticaLocalizacion($fecha_inicio,$fecha);
		$loc->setLocalizacion("Endometritis");
		if($est)
			$loc->setEstablecimiento($est);
		$resultado=$loc->total();

		$g = new GraficoColumnas();		
		$g->setTitulo("Reporte de Endometritis");

		$g->setCategorias($loc->categorias());

		$g->agregarSerie($resultado,"Endometritis");
		$g->setTituloY("Cantidad");
		return $g;
	}

	private function graficoClostridium($fecha_inicio,$fecha,$est=null)
	{
		$clos=new EstadisticaClostridium($fecha_inicio,$fecha);
		if($est)
			$clos->setEstablecimiento($est);
		$resultado=$clos->total();

		$g = new GraficoColumnas();		
		$g->setTitulo("Reporte Infección Gastrointestinal por Clostridium Difficile");

		$g->setCategorias($clos->categorias());

		$g->agregarSerie($resultado,"ITS asociadas a Clostridium");
		$g->setTituloY("Numeros de casos notiicados");
		return $g;
	}

	private function graficoNewCVC($fecha_inicio,$fecha,$est=null)
	{
		$loc=new EstadisticaNewCVC($fecha_inicio,$fecha);
		if($est)
			$loc->setEstablecimiento($est);
		$resultado=$loc->total();

		$g = new GraficoColumnas();		
		$g->setTitulo("Reporte de CVC");

		$g->setCategorias($loc->categorias());

		$g->agregarSerie($resultado,"CVC");
		$g->setTituloY("Cantidad");
		return $g;
	}

	private function graficoUrinario($fecha_inicio,$fecha,$est=null)
	{
		$clos=new EstadisticaLocalizacion2($fecha_inicio,$fecha);
		if($est)
			$clos->setEstablecimiento($est);
		$resultado=$clos->total();

		$g = new GraficoColumnas();		
		$g->setTitulo("Reporte Infección del Tracto Urinario asociado a Catéter Urinario Permanente");

		$g->setCategorias($clos->categorias());

		$g->agregarSerie($resultado,"Infeccion del tracto urinario");
		$g->setTituloY("Numeros de casos notiicados");
		return $g;
	}


}

