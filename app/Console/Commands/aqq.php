<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Establecimiento;
use App\Models\Consultas;
use DB;
use Mail;

class aqq extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'enviar:correo';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Enviar correos camas.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	private $totalLibres=0;
	private $totalReservadas=0;
	private $totalOcupadas=0;
	private $totalBloqueadas=0;
	private $totalReconvertidas=0;


	private function aplanar(&$ests, $campo){
		foreach($ests as &$est){
			$est->{$campo} = new \Illuminate\Database\Eloquent\Collection();
			foreach($est->unidades as $unidad){
				foreach($unidad->{$campo} as $cama){
					$est->{$campo}[] = $cama;
				}
			}
		}
	}

	public function resumenCamasTotal(){/*

		$establecimientos = Establecimiento::whereHas("unidades", function($q){
			$q->where("visible", true)->whereHas("camas", function($qq){
				$qq->vigentes();
			});
		})
        ->with(["unidades" => function($q){
            $q->where("visible", true)
            ->with("camasLibres")
            ->with("camasBloqueadas")
            ->with("camasReservadas")
            ->with("camasOcupadas")
            ->with("camasReconvertidas");
        }])
 
        ->orderBy("nombre", "asc")
        ->get();
		$this->aplanar($establecimientos, "camasLibres");
		$this->aplanar($establecimientos, "camasBloqueadas");
		$this->aplanar($establecimientos, "camasReservadas");
		$this->aplanar($establecimientos, "camasOcupadas");
		$this->aplanar($establecimientos, "camasReconvertidas");
		$res = array();

		foreach($establecimientos as $obj){
			$res[$obj->nombre] = [];
			$res[$obj->nombre]["id"] = $obj->id;
			$res[$obj->nombre]["nombre"] = $obj->nombre;
			$res[$obj->nombre]["libres"] = $obj->camasLibres->count();
			$this->totalLibres += $res[$obj->nombre]["libres"];
			$res[$obj->nombre]["bloqueadas"] = $obj->camasBloqueadas->count();
			$this->totalBloqueadas += $res[$obj->nombre]["bloqueadas"];
			$res[$obj->nombre]["reservadas"] = $obj->camasReservadas->count();
			$this->totalReservadas += $res[$obj->nombre]["reservadas"];
			$res[$obj->nombre]["ocupadas"] = $obj->camasOcupadas->count();
			$this->totalOcupadas += $res[$obj->nombre]["ocupadas"];
			$res[$obj->nombre]["reconvertidas"] = $obj->camasReconvertidas->count();
			$this->totalReconvertidas += $res[$obj->nombre]["reconvertidas"];
		}
		return $res;
*/

$establecimientos = Establecimiento::whereHas("unidades", function($q){
			$q->where("visible", true)->whereHas("camas", function($qq){
				$qq->vigentes();
			});
		})
        ->with(["unidades" => function($q){
            $q->where("visible", true)
            ->where('id','<>',21)   // where para que no sume las camas de Emergencia Adultos
            ->where('id','<>',25);   // where para que no sume las camas de Pabellon Quirurjico
            //->with("camasLibres")
            //->with("camasBloqueadas")
            //->with("camasReservadas")
            //->with("camasOcupadas")
            //->with("camasReconvertidas");
        }])
        ->orderBy("nombre", "asc")
        ->get();

        //return $establecimientos;


        //return $establecimientos;
		//$this->aplanar($establecimientos, "camasLibres");
		//$this->aplanar($establecimientos, "camasBloqueadas");
		//$this->aplanar($establecimientos, "camasReservadas");
		//$this->aplanar($establecimientos, "camasOcupadas");
		//$this->aplanar($establecimientos, "camasReconvertidas");
		$res = array();

		//return $establecimientos;
		$x=0;
		foreach($establecimientos as $obj){

				$camaAmarillo =0;
				$camaRoja     =0;
				$camaNegra    =0;
				$camaAzul     =0;
				$camaVerde    =0;

			$res[$obj->nombre]["nombre"] = $obj->nombre;

			$unidadesArray = array();
			foreach ($obj->unidades as $unidad) {
				
				array_push($unidadesArray, $unidad->alias);
				
				
				$consulta = Consultas::ultimoEstadoCamas();
				$consulta = Consultas::addTiempoBloqueo($consulta);
				$consulta = Consultas::addTiempoReserva($consulta);
				$consulta = Consultas::addTiemposOcupaciones($consulta);
				//return $consulta;
				$consulta = $consulta->where("ue.alias","=",$unidad->alias);
				$consulta = $consulta->addSelect("s.visible");
				$consulta->addSelect(DB::raw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int AS xx"))
				->where("est.id", "=", $obj->id)
				->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
				->whereNotNull("id_sala")
				->where("s.visible", true)
				->orderByRaw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int asc")
				->orderBy("s.nombre", "asc");
				$ocupacionesCamaSala = $consulta->get();




				$res[$obj->nombre]["unidadades"] = $unidadesArray;


			
			foreach($ocupacionesCamaSala as $ocupacion){

			if ($ocupacion->fecha === null){
				if($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";
					$camaAmarillo++;
				}
				elseif($ocupacion->bloqueado !== null){
					$camaNegra++;
					
					
				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$imagen = "camaAzul.png";
					$camaAzul++;
				}
				else{
					$camaVerde++;
					
				}
				//continue;
			}
			else{
				if($ocupacion->ocupado !== null){
					$camaRoja++;
				}
				
				elseif($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";
					
				}	
				elseif($ocupacion->bloqueado !== null){
					$imagen = "camaNegra.png";
					$camaNegra++;
				}	
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$totalAzul++;
				}		
				//continue;
			}

			//$camaVerde =0;


		$res[$obj->nombre]["bloqueadas"] = $camaNegra;
		$res[$obj->nombre]["reconvertidas"] = $camaAzul;
		$res[$obj->nombre]["ocupadas"] = $camaRoja;
		$res[$obj->nombre]["libres"] = $camaVerde;
		$res[$obj->nombre]["reservadas"] = 0;
		} // fin ocupacion




			}  //foreach unidad

		}


		return $res;
		


	}


	public function enviarCorreoContacto(){
		$msg = "";
		$Totales= "";
		$resumen = $this->resumenCamasTotal();
		$total=array(
			"totalLibres"			=> $this->totalLibres,
			//"totalReservadas"		=> $this->totalReservadas,
			"totalOcupadas"			=> $this->totalOcupadas,
			"totalBloqueadas"		=> $this->totalBloqueadas,
			"totalReconvertidas"	=> $this->totalReconvertidas
		);

		foreach($resumen as $r)
		{

						$msg.= "<tr style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>";
						$msg.="<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["nombre"]}</td>";
						$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["libres"]}</td>";
						//$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["reservadas"]}</td>";
						$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["ocupadas"]}</td>";
						$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["reconvertidas"]}</td>";
						$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["bloqueadas"]}</td>";
						$msg.= "</tr>";

		}

						$Totales.= "<tr style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>";
						$Totales.="<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>Total</td>";
						$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalLibres"]}</td>";
						//$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalReservadas"]}</td>";
						$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalOcupadas"]}</td>";
						$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalReconvertidas"]}</td>";
						$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalBloqueadas"]}</td>";
						$Totales.= "</tr>";

		//$corrios=array('erickjamettbello@gmail.com','esjamett@ing.ucsc.cl');

		$corrios=DB::table( DB::raw(
             "(SELECT DISTINCT email from usuarios where tipo='admin' and email !='' and email!='admin@gmail.com' and email!='soporte.raveno@uv.cl' and email!='admin@mail.com') as re"
         ))
		->get();


		Mail::send('emails.Correos', array("Total" => $Totales,"contenido"=>$msg), function($message)use ($cor)
		{	

		    $message->to("fasme2h@gmail.com")->subject('Reporte automático diario: disponibilidad de camas.');
			
		});

			
		/*foreach ($corrios as $cor) 
		{
		Mail::send('emails.Correos', array("Total" => $Totales,"contenido"=>$msg), function($message)use ($cor)
		{	

		    $message->to("fasme2h@gmail.com")->subject('Reporte automático diario: disponibilidad de camas.');
			
		});
		}// fin foreach
		*/
	}// fin funcion
	
	
	
	
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$msg = "";
		$Totales= "";
		$resumen = $this->resumenCamasTotal();
		$total=array(
			"totalLibres"			=> $this->totalLibres,
			//"totalReservadas"		=> $this->totalReservadas,
			"totalOcupadas"			=> $this->totalOcupadas,
			"totalBloqueadas"		=> $this->totalBloqueadas,
			"totalReconvertidas"	=> $this->totalReconvertidas
		);

		foreach($resumen as $r)
		{

						$msg.= "<tr style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>";
						$msg.="<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["nombre"]}</td>";
						$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["libres"]}</td>";
						//$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["reservadas"]}</td>";
						$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["ocupadas"]}</td>";
						$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["reconvertidas"]}</td>";
						$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$r["bloqueadas"]}</td>";
						$msg.= "</tr>";

		}

						$Totales.= "<tr style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>";
						$Totales.="<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>Total</td>";
						$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalLibres"]}</td>";
						//$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalReservadas"]}</td>";
						$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalOcupadas"]}</td>";
						$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalReconvertidas"]}</td>";
						$Totales.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$total["totalBloqueadas"]}</td>";
						$Totales.= "</tr>";

		$corrios=DB::table( DB::raw(
             "(SELECT DISTINCT email from usuarios where tipo='admin' and email !='' and email!='admin@gmail.com' and email!='soporte.raveno@uv.cl' and email!='admin@mail.com') as re"
         ))
		->get();
		
		$i="";		
		foreach ($corrios as $cor) 
		{
			$i=$cor->email;
			
		Mail::send('emails.Correos', array("Total" => $Totales,"contenido"=>$msg), function($message)use ($i)
		{	
		
		    $message->to("fasme2h@gmail.com")->subject('Reporte automático diario: disponibilidad de camas');
			
		});
		}
	}





}