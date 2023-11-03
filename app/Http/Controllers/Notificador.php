<?php
use Carbon\Carbon;
namespace App\Http\Controllers;
class NotificadorController extends Controller{

	public function notificar(){
		if( !Cache::has("hay_cola") ){
			try{
				$date = Carbon::now()->addMinutes(15);
				$expiracion = Carbon::now()->addMinutes(30);
				Queue::later($date, "Notificador@verificar", null, 'notificaciones');
				Cache::add('hay_cola', 'true', $expiracion);
				return "OK";
			}
			catch(Exception $e){
				return "ERROR";
			}
		}

	}

}


class Notificador {

	public function verificar($job, $data){
		if( !Cache::has("hay_cola")){
			return;
		}
		
		Cache::forget("hay_cola");

		$_cupos_servicios = Establecimiento::_cuposParaExtrasistema()
		->select("est.id as id_est",
			"est.nombre as nombre_est",
			"servicio.nombre as nombre_unidad",
			DB::raw("count(cm.id) as cantidad")
		)
		->groupBy("est.id", "est.nombre", "servicio.nombre")
		->orderBy("est.nombre", "asc")->orderBy("servicio.nombre", "asc")
		->get();


		/* Quitar de la lista de cupos aquellos que tienen cero */
		/* Aquí deberían ingresarse sólo aquellos que no han sido notificados dentro del día. */
		$date = Carbon::now()->endOfDay()->addHours(5);
		$this->_cupos_servicios = array_filter($_cupos_servicios, function($i) use ($date){
			return $i->cantidad > 0;
		});

		/* Si depués del filtro la lista resulta vacía, terminar con el script. */
		if ( empty($this->_cupos_servicios) ){
			return;
		}

		$this->arr_ss = array();

		foreach($this->_cupos_servicios as $cupo){
			$this->arr_ss[$cupo->id_est][] = $cupo;
		}

		$_cupos_unidades = Establecimiento::cuposTotalesParaExtrasistema()->get();

		$this->arr_est = array();

		foreach($_cupos_unidades as $cupo){
			$this->arr_est[$cupo->id_est][] = $cupo;
		}

		$this->notificar_ss();
		$this->notificar_est();

		$this->enviarNotificaciones();
		
		$job->delete();

	}

	public function notificar_ss(){
		$msg = "";
		foreach($this->arr_est as $est){
			foreach($est as $cupo){
				$rut = Paciente::formatearRut($cupo->rut, $cupo->dv);
				$msg.= "<tr style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$rut}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->paciente} {$cupo->apellido_paterno} {$cupo->apellido_materno}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->nombre_est}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->est_ex}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->nombre_unidad}</td>";
				$msg.= "</tr>";
			}
		}

		$this->msg_ss = $msg;
		
	}

	public function notificar_est(){
		$this->mensajes = array();
		foreach($this->arr_est as $est){
			$msg = "";
			foreach($est as $cupo){
				$rut = Paciente::formatearRut($cupo->rut, $cupo->dv);
				$msg.= "<tr style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$rut}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->paciente} {$cupo->apellido_paterno} {$cupo->apellido_materno}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->nombre_est}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->est_ex}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->nombre_unidad}</td>";
				$msg.= "</tr>";
			}
			
			$this->mensajes[$cupo->id_est] = $msg;
		}
	}

	public function enviarNotificaciones(){
		$admins_ss = Usuario::whereNotNull("email")->where(function($q) {
			$q->where("tipo", "=", TipoUsuario::ADMINSS)
				->orWhere("tipo", "=", TipoUsuario::MONITOREO_SSVQ);
		})->get();
		$admins = Usuario::whereNotNull("email")->where("tipo", "=", TipoUsuario::ADMIN)->get();

		$admins_ss->each(function($i){
			$email = trim($i->email);
            if($email === '' || $email === null) return;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return;
            }
			if(Cache::has("notifss{$email}")) return;
			$destinatario = "{$i->nombres} {$i->apellido_paterno}";
			Mail::queue("emails.notificacion_ss", array("nombre" => $destinatario, "contenido" => $this->msg_ss), function($message) use ($email, $destinatario) {
				$message->to("{$email}", $destinatario)->subject("Notificación SSVQ");
			});
			$expiracion = Carbon::now()->addHours(24);
			Cache::add("notifss{$email}", true, $expiracion);
		});

		$admins->each(function($i){
			if(isset($this->mensajes[$i->establecimiento])){
				$email = trim($i->email);
                if($email === '' || $email === null) return;
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return;
                }
				if(Cache::has("notifad{$email}")) return;
				$destinatario = "{$i->nombres} {$i->apellido_paterno}";
				Mail::queue("emails.notificacion_ss", array("nombre" => $destinatario, "contenido" => $this->mensajes[$i->establecimiento]), function($message) use ($email, $destinatario) {
					$message->to("{$email}", $destinatario)->subject("Notificación SSVQ");
				});
                $expiracion = Carbon::now()->addHours(24);
				Cache::add("notifad{$email}", true, $expiracion);
			}
		});
	}
}
