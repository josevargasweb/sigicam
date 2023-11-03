<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use Session;
use View;
use Illuminate\Http\Request;
use Auth;
use App\Models\SolicitudContingencia;
use TipoUsuario;
use App\Models\DisponibilidadContingencia;
use App\Models\Usuario;
use PDF;

//define("DOMPDF_ENABLE_REMOTE", true);

class ContingenciaController extends Controller{

	public function viewContingencia(){
		$establecimientos=Establecimiento::getAll(Session::get("idEstablecimiento"));
		$largo=count($establecimientos);
		return View::make("Contingencia/FormularioContingencia", ["establecimientos" => $establecimientos, "largo" => $largo]);
	}

	public function registrarContingencia(Request $request){
		try{
			$solicitante=trim($request->input("solicitante"));
			$comentarioEspera=trim($request->input("comentarioEspera"));
			$comentarioBasica=trim($request->input("comentarioBasica"));
			$comentarioCompleja=trim($request->input("comentarioCompleja"));
			$pacienteCompleja=$request->input("pacienteCompleja");
			$cupos = $request->input("cupos");
			$idUGCC=$request->input("idugcc");

			$ids = null;
			if(count($idUGCC) != 0){
				$ids = "{".implode(",", $idUGCC)."}";
			}

			$idUsuario=Auth::user()->id;
			$solicitud=new SolicitudContingencia;
			$solicitud->usuario=$idUsuario;
			if(!empty($solicitante)) $solicitud->solicitante=$solicitante;
			$solicitud->fecha=\Carbon\Carbon::now()->format('Y-m-d H:i:s');
			$solicitud->n_pacientes_espera=$request->input("pacienteEspera");
			if(!empty($comentarioEspera))$solicitud->comentario_espera=$comentarioEspera;
			$solicitud->n_pacientes_basica=$request->input("pacienteBasica");
			$solicitud->idugcc = $ids;
			if(!empty($comentarioBasica))$solicitud->comentario_basica=$comentarioBasica;
			if(!empty($pacienteCompleja)) $solicitud->n_pacientes_compleja=$pacienteCompleja;
			if(!empty($comentarioCompleja)) $solicitud->comentario_compleja=$comentarioCompleja;
			$solicitud->establecimiento=Session::get("idEstablecimiento");
			$solicitud->cupos = (int)$cupos;
			$solicitud->valido=true;
			$solicitud->save();
			$idSolicitud=$solicitud->id;

			$hospitales=$request->input("hospital");
			$nombres=$request->input("nombre");
			$cantidades=$request->input("cantidad");
			$comentarios=$request->input("comentario");

			for($i=0; $i<count($hospitales); $i++){
				$disponibilidad=new DisponibilidadContingencia;
				$disponibilidad->solicitud=$idSolicitud;
				$disponibilidad->establecimiento=$hospitales[$i];
				$disponibilidad->persona=trim($nombres[$i]);
				$cantidad = (int) trim($cantidades[$i]);
				$disponibilidad->n_camas=$cantidad;
				$disponibilidad->comentario=trim($comentarios[$i]);
				$disponibilidad->save();
			}
			return response()->json(array("exito" => "Se ha registrado la declaración  de contingencia."));
		} catch(Exception $ex){
			return response()->json(array("msg" => $ex->getMessage(), "error" => "Se ha producido un error al realizar la reserva"));
		}
	}

	public function getContingencias(){


		$us = Auth::user();
		$tipoUsuario = $us->tipo;
		$idUsuario = $us->id;
		$idEstablecimiento = $us->establecimiento;
		$response=array();
		$contingencias=($tipoUsuario == TipoUsuario::ADMINSS || $tipoUsuario == TipoUsuario::MONITOREO_SSVQ) ? SolicitudContingencia::getContingencias() : SolicitudContingencia::getContingenciasPorEstablecimiento($idEstablecimiento);
		foreach ($contingencias as $contingencia) {
			$hospitales=DisponibilidadContingencia::getHospitalesPorContingencia($idUsuario, $contingencia->id, $tipoUsuario);
			$fila=array(
				implode(",", $hospitales),
				$contingencia->solicitante,
				$contingencia->n_pacientes_espera,
				$contingencia->n_pacientes_basica,
				$contingencia->n_pacientes_compleja,
				date("d/m/Y", strtotime($contingencia->fecha)),
				View::make("Contingencia/AccionTablaContingencia", [
					"id" => $contingencia->id])->render()
			);
			if($tipoUsuario == TipoUsuario::ADMINSS || $tipoUsuario == TipoUsuario::MONITOREO_SSVQ) array_unshift($fila, Establecimiento::getNombre($contingencia->establecimiento));
			$response[]=$fila;
		}
		return response()->json($response);
	}

	public function tablaContingencias(){
		return View::make("Contingencia/TablaContingencia");
	}

	public function getEstablecimientos(Request $request){
		$idEstablecimiento=$request->input("id");
		$response=Establecimiento::getDiferentes(Session::get("idEstablecimiento"), $idEstablecimiento);
		return response()->json($response);
	}

	public function verPDF($id){
		$solicitud=SolicitudContingencia::find($id);
		$user=Usuario::find($solicitud->usuario);
		$establecimiento=Establecimiento::getNombre($user->establecimiento);
		$disponibilidad=$solicitud->hasMany("App\Models\DisponibilidadContingencia", "solicitud")->get();
		$titulo = "Declaración de contingencia {$establecimiento} {$solicitud->fecha}";
		$html = view('Contingencia/PDFContingencia',["solicitud" => $solicitud, "disponibilidad" => $disponibilidad, "establecimiento" => $establecimiento, "titulo" => $titulo])->render();

    	return PDF::load($html)->show();
	}

	public function descargarPDF($id){
		$solicitud=SolicitudContingencia::find($id);
		$user=Usuario::find($solicitud->usuario);
		$establecimiento=Establecimiento::getNombre($user->establecimiento);
		$disponibilidad=$solicitud->hasMany("App\Models\DisponibilidadContingencia", "solicitud")->get();
		$titulo = "Declaración de contingencia {$establecimiento} {$solicitud->fecha}";
		$html = view("Contingencia/PDFContingencia", ["solicitud" => $solicitud, "disponibilidad" => $disponibilidad, "establecimiento" => $establecimiento, "titulo" => $titulo])->render();
		return PDF::load($html, 'letter', 'portrait')->download();
	}

	public function verContingencia($id){

		$solicitud=SolicitudContingencia::find($id);
		$user=Usuario::find($solicitud->usuario);
		$establecimiento=Establecimiento::getNombre($solicitud->establecimiento);
		$disponibilidad=$solicitud->hasMany("App\Models\DisponibilidadContingencia", "solicitud")->get();
		Session::put("contingencia-disponibilidad", $disponibilidad);

		return View::make("Contingencia/VerContingencia", ["solicitud" => $solicitud, "disponibilidad" => $disponibilidad, "establecimiento" => $establecimiento]);
	}

	public function actualizarContingencia(Request $request){
		$disponibilidad = Session::get("contingencia-disponibilidad");
		foreach($disponibilidad as $d){
			$real = $request->input("real-{$d->establecimiento}");
			if( isset($real) && !isset($d->n_camas_reales )){
				try {
					$d->n_camas_reales = $real;
					$d->save();
				}
				catch(Exception $e){
				}
			}
		}

		$cupos = $request->input("cupos");
		$fecha = $request->input("fecha"); 
		$solicitud = SolicitudContingencia::find($request->input("idSolicitud"));
		$solicitud->cupos = (int)$cupos;
		$solicitud->fecha = $fecha;
		$solicitud->save();

		return response()->json(["exito" => "Datos actualizados", "solicitud"=>$solicitud, "fecha"=>$fecha]);
	}

	public function anularContingencia(Request $request){
		try{
			$id=$request->input("id");
			$contingencia=SolicitudContingencia::find($id);
			$contingencia->valido=false;
			$contingencia->save();

			return response()->json(array("exito" => "La contingencia ha sido anulada"));

		} catch(Exception $ex){
			return response()->json(array("msg" => $ex->getMessage(), "error" => "Error al anular la contingencia"));
		}
	}

}

?>