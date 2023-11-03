<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CamaTemporal;
use DB;
use Session;
use App\util\TipoUsuario;
use URL;
use App\Models\Consultas;
use App\Models\Usuario;

class CamaTemporalController extends Controller{

	public function moverACamaTemporal(Request $request){
		try{
			DB::beginTransaction();
			
			$cdt = new CamaTemporal();
			$cdt->ocultarCaso($request->caso);
			$cdt->moverACamaTemporal($request->caso);
			$cdt->cerrarHistorial($request->caso);
			DB::commit();
			return response()->json(["error" => false]);
		}catch(\Exception $e){
			DB::rollBack();
			return response()->json(["error" => true,"msj" => $e->getMessage()]);
		}
	}
	public function traerCamas(Request $request){
		try{
			$ct = new CamaTemporal();
			$camas = $ct->traerCamasDeUnidad($request->unidad);
			
			$unidad_obj =  Session::get("unidades")->KeyBy("id")->get($request->unidad);
			$no_some =  $unidad_obj->some === null && Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO && Session::get("usuario")->tipo !== TipoUsuario::IAAS;
			
			$imagen="SIN_PACIENTE.png";

			$reconvertida = "nada.png";
			$sexo = "nada.png";
			$estadia_promedio = "nada.png";
			$alta_clinica = "nada.png";
			$iaas_img = "nada.png";
			$derivado = "nada.png";
			$pabellon = "nada.png";
			
			$url = URL::to('/');
			
			$descripcionCama = "";
			
			$datos_camas = [];
			
			$opcion = "";
			
			foreach($camas as $cama){
				if($cama->en_pabellon){
					$pabellon = "pabellon2.png";
				}
				if($cama->derivado){
					$derivado = "derivado.png";
				}
				$sexo = $this->imagenSexo($cama->sexo);
				
				$opcion = $this->ocupado($cama, $imagen, $reconvertida, $sexo, $estadia_promedio, $alta_clinica, $iaas_img, $derivado, $pabellon, $url);
				
				if($cama->reservado !== null){
					$opcion = $this->reservado($cama, $url, $no_some, $descripcionCama);
				}
				/*else if($cama->bloqueado !== null){
					$opcion = $this->bloqueado($cama, $descripcionCama,$url);
				}*/
				$datos_camas[] = $opcion;
			}
			
			return response()->json(["error" => false,"camas" => $datos_camas]);
		} catch (\Exception $ex) {
			return response()->json(["error" => true,"msj" => $ex->getMessage().", ".$ex->getFile().", ".$ex->getLine()]);
		}
	}
	private function imagenSexo($sexo){
		
		switch($sexo){
			case "masculino":
				return "hombre.png";
			case "femenino":
				return "mujer.png";
			case "indefinido":
				return "indefinido.png";
			case "desconocido":
				return "desconocido.png";
			default:
				return "";
		}
	}
	private function ocupado($cama,$imagen,$reconvertida,$sexo,$estadia_promedio,$alta_clinica,$iaas_img,$derivado,$pabellon,$url){
		$horas = Consultas::formatTiempoOcupacion($cama->fecha_ingreso_real, $cama->fecha_liberacion);

		$caption = (empty($cama->riesgo)) ? $horas : $cama->riesgo ." - ". $horas;

		$descripcionCama = ($cama->cama_descripcion)?" (<strong style='color:black'>$cama->cama_descripcion</strong>)":"";

		$caption = $caption." - ".$cama->id_cama." <br>".$descripcionCama;

		if($cama->tiene_infeccion)
		{
			$iaas_img = "iaas.png";
		}

		if($cama->riesgo == null){

			//Ultima Usada
			$respuesta2 = Consultas::restriccionCategorizacionCama($cama->id_caso);
			$resultado = $respuesta2->original;

			$imagen = $resultado["imagen"];
			$restriccion_tiempo = $resultado["restriccion"];
		}

		//cama ANARILLA
		elseif($cama->riesgo == "B3" || $cama->riesgo == "C1" || $cama->riesgo == "C2"){
			$imagen = "RIESGO_B.png";
		}

		//Cama VERDE
		elseif($cama->riesgo[0]=="D" || $cama->riesgo == "C3"){
			$imagen = "RIESGO_D.png";
		}

		//Cama ROJA
		elseif($cama->riesgo[0]=="A" ||$cama->riesgo == "B1" || $cama->riesgo == "B2"){
			$imagen = "RIESGO_A.png";
		}

		//demas usuarios no ven los riesgos que ponen los de urgencia
		if($cama->id_usuario && Session::get('usuario')->tipo != 'usuario'){

			$tipo_usuario = Usuario::find($cama->id_usuario,['tipo']);
			if($tipo_usuario->tipo == 'usuario'){
				$imagen = "SIN_CATEGORIZACION.png";
				$caption = $horas;
				$caption = $caption." - ".$cama->id_cama." <br>".$descripcionCama;
			}
		}

		//cama naranja
		if($cama->fecha_ingreso_real == null){
			$imagen = "cama_reservada.png";
		}

		if($cama->fecha_liberacion == null && $cama->fecha_alta != null){
			$alta_clinica = "alta_clinica.png";
		}
		if($cama->estadia_promedio)
		{
			if (strstr($horas, 'd', true) > $cama->estadia_promedio) {
				$estadia_promedio = "sobre_promedio.png";
			}
		}

		$click="onclick='marcarCama(-1, -1, false);getPacienteCamaTemporal(\"$cama->id_caso\",this)' data-id= $cama->id_paciente data-cama=$imagen";
		$class="class='cursor $cama->id_paciente'";
		$opcion="<div class='col-md-3 divContenedorCama' style='margin-top: 0px' data-nombre='{$cama->nombre_completo}' data-toggle='tooltip' data-placement='top' data-original-title='{$cama->nombre_completo}'>

			<a $click $class>  <figure> <img src='$url/img/$imagen' class='imgCama' />

		<figcaption>
		<img src='$url/img/$sexo' class='imgPunto' />
		<img src='$url/img/$reconvertida' class='imgPunto' />
		<img src='$url/img/$estadia_promedio' class='imgPunto' />
		<img src='$url/img/$alta_clinica' class='imgPunto' />
		<img src='$url/img/$iaas_img' class='imgPunto' />
		<img src='$url/img/$derivado' class='imgPunto' />
		<img src='$url/img/$pabellon' class='imgPunto' />
			<br>
			$caption
		</figcaption> </figure> </a></div>";
		
		return $opcion;
	}
	private function reservado($cama,$url,$no_some,$descripcionCama){
		
		$imagen = "camaAmarillo.png";
		
		$horas = Consultas::formatTiempoReserva($cama->reserva_queda);
		
		if (empty($horas)) {
			$horas = "<br><br>";
		}
		$renovada = ($cama->renovada) ? 1 : 0;
		$click="";
		
		if ($no_some) {
			$click = "onclick='marcarCama(-1, -1, false);getPacienteCamaTemporal($cama->id_caso,this)' data-id= $cama->id_paciente";
		}

		$opcion="<a class='cursor $cama->id_paciente' $click>  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $cama->id_cama $descripcionCama</figcaption> </figure> </a>";
		
		return $opcion;
	}
	/*private function bloqueado($cama,$descripcionCama,$url){

		$imagen = "camaNegra.png";
		$horas = Consultas::formatTiempoBloqueo($cama->fecha_bloqueo);
		if (empty($horas)) {
			$horas = "<br><br>";
		}
		$click = "onclick='abrirDesbloquear(\"$cama->id_cama_unq\");'";
		$class = "class='cursor'";
		$opcion = "<a $class $click><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $cama->id_cama $descripcionCama</figcaption> </figure></a>";

		return $opcion;		
	}*/
	public function getPacienteCamaTemporal(Request $request){
		try{
			$ct = new CamaTemporal();
			$datos = $ct->infoPaciente($request->caso);
			
			return response()->json(["error" => false,"datos" => $datos]);
		} catch (\Exception $ex) {
			return response()->json(["error" => true,"msj" => $ex->getMessage()]);
		}
	}
}

