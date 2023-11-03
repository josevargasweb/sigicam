<?php

namespace App\Http\Controllers;

use App\Models\ElectroNeuro;
use Illuminate\Http\Request;
use PDF;

class ElectroNeuroController extends Controller
{
	public function datosPaciente(Request $request){
		try{
			$caso = base64_decode($request->caso);
			$en = new ElectroNeuro();
			$datos = $en->datosPaciente($caso);
			
			return response()->json(["error" => false,"datos" => $datos]);
		} catch (\Exception $ex) {
			return response()->json(["error" => true,"msg" => $ex->getMessage()]);
		}
	}
	public function agregar(Request $request){
		try{
			$caso = base64_decode($request->idCaso);
			$request->merge(["caso" => $caso]);
			$en = new ElectroNeuro();
			$en->guardar($request);
			
			return response()->json(["error" => false, "msg" => "Solicitud de examen guardada correctamente"]);
		} catch (\Exception $ex) {
			return response()->json(["error" => true,"msg" => $ex->getMessage()]);
		}
	}
	public function historial(Request $request){
		try{
			$en = new ElectroNeuro();
			$historiales = $en->historial(base64_decode($request->caso));
			$datos = [
				"draw"          => intval($request->input('draw')),
				"recordsTotal"  => count($historiales),
				"recordsFiltered" => count($historiales),
				"data"          => $historiales
			];
			return response()->json($datos);
		} catch (\Exception $ex) {
			$datos = [
				"draw"          => intval($request->input('draw')),
				"recordsTotal"  => 0,
				"recordsFiltered" => 0,
				"data"          => []
			];
			return response()->json($datos);
		}
	}
	public function eliminar(Request $request){
		try{
			$en = new ElectroNeuro();
			$en->eliminar($request->id);
			
			return response()->json(["error" => false,"msg" => "Se ha eliminado correctamente"]);
		} catch (\Exception $ex) {
			return response()->json(["error" => true,"msg" => $ex->getMessage()]);
		}
	}
	public function cargar(Request $request){
		try{
			$en = new ElectroNeuro();
			$datos = $en->cargar($request->id);
			
			return response()->json(["error" => false,"datos" => $datos]);
		} catch (\Exception $ex) {
			return response()->json(["error" => true,"msg" => $ex->getMessage()]);
		}
	}
	public function pdf(Request $request){
		try{
			$en = new ElectroNeuro();
			$datos = $en->pdf($request->id);
			$fecha = date("d-m-Y H:i:s");
			$pdf = PDF::loadView('Gestion.gestionMedica.pdf.PdfElectroNeuro',
				["datos" => $datos]
			);
			
			return $pdf->stream('examen_electromiografia_neuroconduccion_'.$fecha.'.pdf');
		}catch(\Exception $e){
			
		}
	}
}
