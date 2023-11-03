<?php

namespace App\Http\Controllers;


use DB;
use Debugbar;
use Log;
use Config;
use Illuminate\Http\Request;
use Excel;
use PHPExcel_Calculation;
use App\Models\EgresoIdentificacionPacienteVista;

class AccessController extends Controller
{
	//
	
	public function xlsView(){

		return View("access.xlsView");
	}
    public function generarXls($inicio, $fin,$estab){
		Config::set(['excel.export.calculate' => true]);

		
		$contact = EgresoIdentificacionPacienteVista::select()->whereRaw("fecha_liberacion >= '".$inicio."' AND fecha_liberacion <= '".$fin." 23:59:59'")
		->where("id_establecimiento","=",$estab)->get()->toArray();

		Excel::load("storage/data/plantilla/esquema_access.xlsx", function($file) use ($contact)
		{     
			$sheet1 = $file->setActiveSheetIndex(0);
			//$file->calculate();


			Excel::create('access', function($excel) use ($sheet1, $contact)  {

				$excel->addExternalSheet($sheet1);
				
				//$sheet1->fromArray($contact,null,"A2");
				//$sheet1->setCellValue("A2", $contact);
				$i=0;
				$arreglo = array();
				foreach($contact as $row){

					$caso = $row["caso"];
					$traslados = DB::select('SELECT public."retornar_egresos_traslados"('.$caso.')')[0]->retornar_egresos_traslados;
					$traslados = str_replace("(","",$traslados);
					$traslados = str_replace(")","",$traslados);
					$traslados = explode(",",$traslados);

					$diagnosticos = DB::select('SELECT public."retornar_egresos_diagnosticos"('.$caso.')')[0]->retornar_egresos_diagnosticos;
					$diagnosticos = str_replace("(","",$diagnosticos);
					$diagnosticos = str_replace(")","",$diagnosticos);
					$diagnosticos = explode(",",$diagnosticos);

					$arreglo[] = array($row["num_adm"], $row["num_egr"], $row["estab"], $row["ser_salud"],$row["ficha"], $row["apell_pate"], $row["apell_mate"], $row["nombres"], $row["tipo_id"], $row["rut"], $row["dv"], $row["pasaporte"], $row["sexo"], $row["d_nac"], $row["m_nac"], $row["a_nac"], $row["edad_cant"], $row["tipo_edad"], $row["etnia"], $row["t_etnia"], $row["p_origen"], $row["instrucc"], $row["tipo_ocupacion"], $row["glo_ocupacion"], $row["telefono"], $row["movil"], $row["domicilio"], $row["numero_via"], $row["via"], $row["comuna"], $row["previ"], $row["benef"], $row["mod"], $row["ley_prev"], $row["acc_aten"], $row["procedenci"], $row["nom_hosp_p"], $row["cod_hosp_p"], $row["hora_ing"], $row["min_ing"], $row["dia_ing"], $row["mes_ing"], $row["ano_ing"], $row["area_func_i"], $row["ser_clin_i"], $traslados[0], $traslados[1], $traslados[2], $traslados[3], $traslados[4], $traslados[5], $traslados[6], $traslados[7], $traslados[8], $traslados[9], $traslados[10], $traslados[11], $traslados[12], $traslados[13], $traslados[14], $traslados[15], $traslados[16], $traslados[17], $traslados[18], $traslados[19], $traslados[20], $traslados[21], $traslados[22], $traslados[23], $traslados[24], $traslados[25], $traslados[26], $traslados[27], $traslados[28], $traslados[29], $traslados[30], $traslados[31], $traslados[32], $traslados[33], $traslados[34], $traslados[35], $traslados[36], $traslados[37], $traslados[38], $traslados[39], $traslados[40], $traslados[41], $traslados[42], $traslados[43], $traslados[44],"","","","","","","","","","",$diagnosticos[0],$diagnosticos[1],$diagnosticos[2],$diagnosticos[3],$diagnosticos[4],$diagnosticos[5],$diagnosticos[6],$diagnosticos[7],$diagnosticos[8],$diagnosticos[9],$diagnosticos[10],$diagnosticos[11],$diagnosticos[12],$diagnosticos[13],$diagnosticos[14],$diagnosticos[15],$diagnosticos[16],$diagnosticos[17],$diagnosticos[18],$diagnosticos[19],$diagnosticos[20],$diagnosticos[21]);
				}

				

				$sheet1->fromArray($arreglo, null, "A2");

			})->export('xlsx');
        });
        
	}
	
	public function getDatos(Request $request){

		return $request->all();
		return "K";
	}
}

//CAMAS  VIVIENDAS  RAVENO  TURISMO/MUSEO
/*

Camas -> F / Ed / Al
Viviendas -> Nico /
Raveno -> Fran / Nola / Mati /Sebastian /
Museo -> 

*/
