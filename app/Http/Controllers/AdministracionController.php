<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Establecimiento;
use App\Models\EvolucionCaso;
use App\Models\Riesgo;
use App\Models\Paciente;
use HTML;
use Consultas;
use View;
use URL;
use Auth;
use DB;
use PDF;
use Excel;

class AdministracionController extends Controller{

	public function gestionCamasAdm(){
		return View::make("Administracion/Camas");
	}

	public function gestionSalasAdm(){
		return View::make("Administracion/Salas");
	}

	public function gestionServiciosAdm(){
		return View::make("Administracion/Servicio");
	}

	public function gestionEstablecimientosAdm(){
		$region = Establecimiento::where("id",Auth::user()->establecimiento)->first();
		if($region){
			$establecimientos=Establecimiento::obtenerHospitalesRegion($region->id_region);
		}else{
			$establecimientos=Establecimiento::obtenerTodos();
		}
		$response=array();
		foreach($establecimientos as $establecimiento){
			$editar="<a class='cursor' onclick='editar(\"$establecimiento->nombre\", \"$establecimiento->complejidad\", $establecimiento->id)'>Editar</a>";
			$link=HTML::link("/administracionUnidad/unidad/$establecimiento->id", "$establecimiento->nombre");
			$response[]=array("nombre" => $link, "complejidad" => ucwords($establecimiento->complejidad), "editar" => $editar);
		}
		return View::make("Administracion/Establecimiento", ["establecimientos" => $response, "complejidad" => Consultas::obtenerEnum("complejidad")]);
	}

	public function obtenerEstablecimientos(){
		$region = Establecimiento::where("id",Auth::user()->establecimiento)->first();
		if($region){
			$establecimientos=Establecimiento::obtenerHospitalesRegion($region->id_region);
		}else{
			$establecimientos=Establecimiento::obtenerTodos();
		}
		$response=array();
		foreach($establecimientos as $establecimiento){
			$editar="<a class='cursor' onclick='editar(\"$establecimiento->nombre\", \"$establecimiento->complejidad\", $establecimiento->id)'>Editar</a>";
			$link=HTML::link("/administracionUnidad/unidad/$establecimiento->id", "$establecimiento->nombre");
			$response[]=array("<div>".$link."</div>",ucwords($establecimiento->complejidad),$editar);
		}
		return response()->json(["aaData" => $response]);
	}

	private function getOpciones($nEst, $nUnidad, $nSala, $cantSala, $idSala){
		$opciones="
		<a class='cursor' onclick='editar(\"$nEst\", \"$nUnidad\", \"$nSala\", $cantSala,\"$idSala\")'>Editar</a>&nbsp;&nbsp;&nbsp;
		<a class='cursor' onclick='eliminar(".$idSala.")'>Eliminar</a>";
		return $opciones;
	}


	public function editarCama(Request $request){
		try{
			$cantVieja=(int)$request->input("cant-vieja");
			$cantNueva=(int)trim($request->input("cant-cama"));
			$idSala=$request->input("u-sala");
			$idCama=$request->input("cama");
			if($cantVieja>$cantNueva){
			}else{
				$cama = new Cama();
			}
			return response()->json(array("exito" => ''));
		} catch(Exception $ex){
			return response()->json(array("mgs" => $ex->getMessage(), "error" => ""));
		}
	}

	public function editarEstablecimiento(Request $request){
		try{
			$id=$request->input("idEstab");
			$nombre=trim($request->input("nombre"));
			$complejidad=$request->input("complejidad");

			$estab=Establecimiento::find($id);
			$estab->nombre=$nombre;
			$estab->complejidad=$complejidad;
			$estab->save();

			return response()->json(array("exito" => ""));

		} catch(Exception $ex){
			return response()->json(array("mgs" => $ex->getMessage(), "error" => ""));
		}
	}

	public function indexNoCategorizados(){
		return View::make("Administracion/IndexNoCategorizado");
	}

	public function infoPacienteNoCategorizado(Request $request){
		return EvolucionCaso::consultaPacienteNoCategorizado($request->rut);
	}

	public function categorizarNoCategorizados(Request $request){
		try{
            DB::beginTransaction();

            foreach ($request->categorizacion as $key => $id_cat) {
				if($id_cat != "" || $id_cat != null){
					
					$riesgo = new Riesgo;
					$riesgo->categoria = strtoupper($id_cat);
					if( isset($request->r1[$key]) && $request->r1[$key] != "" ){		
						
						$riesgo->riesgo1 = $request->r1[$key]; 
						$riesgo->riesgo2 = $request->r2[$key];
						$riesgo->riesgo3 = $request->r3[$key];
						$riesgo->riesgo4 = $request->r4[$key];
						$riesgo->riesgo5 = $request->r5[$key];
						$riesgo->riesgo6 = $request->r6[$key];
						$riesgo->riesgo7 = $request->r7[$key];
						$riesgo->riesgo8 = $request->r8[$key];
						
						$riesgo->dependencia1 = $request->d1[$key];
						$riesgo->dependencia2 = $request->d2[$key];
						$riesgo->dependencia3 = $request->d3[$key];
						$riesgo->dependencia4 = $request->d4[$key];
						$riesgo->dependencia5 = $request->d5[$key];
						$riesgo->dependencia6 = $request->d6[$key];
					}
					$riesgo->save();
	
					$evol = EvolucionCaso::where("id", $request->id_evolucion[$key])->first();
					$evol->riesgo = strtoupper($id_cat);
					$evol->riesgo_id = $riesgo->id;
					$evol->id_usuario = Auth::user()->id; 
					$evol->save();
				}
			}

            DB::commit();
            return response()->json(["exito" => "Se han llevado a cabo las actualizaciones de riesgo"]);
            
        }catch(Exception $e){
            DB::rollback();
            return response()->json(["error" => "Error al actualizar riesgo dependencias"]);
        }
		
	}

	public function reporteNoCategorizados($rut,$reporte){
		$data = EvolucionCaso::consultaPacienteNoCategorizado($rut);
		$dataPaciente = Paciente::where('rut',$rut)->first(['nombre','apellido_paterno','apellido_materno','rut','dv']);
		$idEstablecimiento = Auth::user()->establecimiento;
		$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
		if($reporte == 'pdf'){
			$html = PDF::loadView("TemplatePDF/reportePacienteNoCategorizado",[
				"paciente" => $dataPaciente,
				"datos" => $data,
				"establecimiento" => $nombreEstablecimiento
			]);
			return $html->setPaper('legal','portrait')->download('DiasSinCategorizar-'.$rut.'-'.$dataPaciente->dv.'.pdf');
		}else{
			Excel::create('DiasSinCategorizar', function($excel) use ($data,$dataPaciente,$nombreEstablecimiento) {
				$excel->sheet('DiasSinCategorizar', function($sheet) use ($data,$dataPaciente,$nombreEstablecimiento) {
	
					$sheet->mergeCells('A1:B1');
					$sheet->setAutoSize(true);
					$sheet->setHeight(1,50);
					$sheet->row(1, function($row) {
	
						$row->setBackground('#1E9966');
						$row->setFontColor("#FFFFFF");
						$row->setAlignment("center");
	
					});
	
					$sheet->loadView('Estadisticas.reporteCategorizacion.excelReportePacienteNoCategorizado', [
						"paciente" => $dataPaciente,
						"datos" => $data,
						"establecimiento" => $nombreEstablecimiento
						]
					);
				});
			})->download('xls');
		}
	}
}

?>
