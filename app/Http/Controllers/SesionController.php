<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Models\User;
use TipoUsuario;
use App\Models\Establecimiento;
use App\Models\UnidadEnEstablecimiento;
use App\Models\AreaFuncional;
use Redirect;
use Mail;
use DB;
use App\Models\Sesion;
use Log;
use Carbon\Carbon;
use App\Models\Usuario;

class SesionController extends Controller
{
    public function login(){
		return View::make("Sesion/login");
	}


	protected function getUnidades(){
		$unidades = UnidadEnEstablecimiento::conCamas2()->where("visible", true)->where("establecimiento", Session::get('idEstablecimiento'))
			->select("id", "alias as nombre", "url as alias", "establecimiento", "some", "id_area_funcional")->get();
			return $unidades;
	}

	public function getAreaFuncional(){
		$area=DB::select(DB::raw("SELECT a.nombre, u.id_area_funcional 
								FROM unidades_en_establecimientos u, area_funcional a
								WHERE u.id_area_funcional= a.id_area_funcional
								AND u.visible = true
								AND u.establecimiento = ".Session::get('idEstablecimiento')."
								group by u.id_area_funcional, a.nombre
								order by a.nombre"));
		return $area;
	}

	public function doLogin(Request $request){
		
		
		$rut=substr(trim($request->input("rut")), 0, -1);
		$password=trim($request->input("password"));
		
		$response=array();
		$userData=array("rut" => $rut, "password" => $password, "visible" => true);



		if(Auth::attempt($userData)){
			$user = Auth::user();
			Session::put("usuario", $user);

			//print_r($user);
			//return $user;
			if ($user->tipo != TipoUsuario::ADMINSS && $user->tipo != TipoUsuario::MONITOREO_SSVQ && $user->tipo != TipoUsuario::ADMINIAAS){
				$idEstablecimiento=$user->establecimiento;
				$establecimiento = Establecimiento::find($idEstablecimiento);
				$permisos_establecimiento = $establecimiento->getPermisos();
				$nombre=ucwords($establecimiento->nombre);
				$some=(is_null($establecimiento->some)) ? false : true;
				Session::put("permisos_establecimiento", $permisos_establecimiento);
				Session::put("idEstablecimiento", $idEstablecimiento);
				Session::put("complejidad", $establecimiento->complejidad);
				Session::put("nombreEstablecimiento", $nombre);
				$unidades  = $this->getUnidades();
				Session::put("unidades", $unidades);
				Session::put("some", $some);
				$area = $this->getAreaFuncional();
				Session::put("area", $area);

			}

			//guardar fecha y usuario que inicio sesion
			$inicio_sesion = new Sesion;
			$inicio_sesion->id_usuario = Auth::user()->id;
			$inicio_sesion->save();
			$u = Session::get('usuario');
			$hoy = now()->format("Y-m-d H:i:s");
			$usuario = Usuario::find($u->id);
			$ultima = $usuario->updated_at;
			if($ultima == ""){

			}
			else{
				$ultima = $usuario->updated_at;
				$ultima = Carbon::parse($usuario->updated_at);
				
			}
			$ingreso = $usuario->fecha_ingreso;
			$ingreso = Carbon::parse($ingreso);

			//cambio la clave antes ?
			if($ultima == ""){
				$tiempo = $ingreso->diffInMonths($hoy);
				
			}else{
				$tiempo = $ultima->diffInMonths($hoy);
				
			}

			if($tiempo >= 4){
				$response=array("href" => "administracion/cambiarContraseña");
			}else{
				if($user->tipo == "oirs")
				{
					$response=array("href" => "registro-visitas");
				}
				else
				{
					$response=array("href" => "index");
				}
			}

		}
		else $response=array("error" => "Usuario deshabilitado y/o contraseña invalida");


		return response()->json($response);


	}

	public function actualizarFechaClave(){
		try {
			$hoy = now()->format("Y-m-d H:i:s");
		$id = Auth::user()->id;
		$usuario = Usuario::find($id);
		$usuario->updated_at = $hoy;
		$usuario->save();
		$response=array("exito" => "Se le volvera a notificar dentro de 4 meses.");
		} catch (exception $ex) {
			$response=array("error" => "Ha ocurrido un error, por favor notificar a un supervisor.");
		}

		return response()->json($response);
	}

	public function cerrarSesion(){
		Auth::logout();
		//Hacer un update de usuairo para marcar  logout usuario
		Session::flush();
		return Redirect::to('/');
	}

	/* correo que se envia en el formulario de contacto del login */
	public function enviarCorreoContacto(Request $request){
		$nombres=trim($request->input("nombreContacto"));
		$correo=trim($request->input("correoContacto"));
		$comentariocorreo = trim($request->input("comentarioContacto"));
		$asuntocorreo = ($request->has("asuntoContacto")) ? trim($request->input("asuntoContacto")) : null;

		$comentario = ($asuntocorreo != null) ? $asuntocorreo."\n\n".$comentariocorreo : $comentariocorreo;

		$data = array(
			"nombre" => $nombres,
			"correo" => $correo,
			"texto" => $comentario,
			"asunto"=>$asuntocorreo
			);
		$asunto= "Contacto SIGICAM";
		$destinatario = "Soporte";
		$correos = array($correo, "labitec@uv.cl");
		Mail::send('Sesion.Correos', $data, function($message) use ($correos, $destinatario, $asunto)
		{
		  $message->to($correos, $destinatario)
		          ->subject($asunto);
		});
		return response()->json(array("exito" => "El correo ha sido enviado"));
	}
}
