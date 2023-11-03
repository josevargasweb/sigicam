<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Auth;
use View;
use Log;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Usuario;
use Consultas;
use App\Models\Establecimiento;
use App\Models\UnidadEnEstablecimiento;
use App\Models\Restriccion;
use App\Models\Especialidades;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mail;


class AdministrarUsuarioController extends Controller{

	
	public function registrarCambioUsuario(Request $request){
		
		try{
			DB::beginTransaction();
			$usuario = Usuario::find($request->idUsuario);

			$usuario->tipo = $request->tipoUsuario;
			$usuario->establecimiento = (int)$request->establecimiento;
			$usuario->rut = $request->rut;

			if($request->dv == "K" || $request->dv == "k"){ 
				$usuario->dv = 10; 
			}else{ 
				$usuario->dv = $request->dv; 
			} 
			$usuario->visible = ($request->visible == 1) ? 1 : 0;
			$usuario->email = $request->email;
			$usuario->nombres = $request->nombre;
			$usuario->apellido_paterno = $request->apellido_paterno;
			$usuario->apellido_materno = $request->apellido_materno;
			if($usuario->tipo == "secretaria") $usuario->usuario_especialidad= $request->especialidad;
			
			$usuario->updated_at = Carbon::now()->toDateTimeString();
			$usuario->iaas = ($request->gestor_iaas == "si") ? 1 : 0;

			if($request->password != ""){
				$usuario->password = bcrypt($request->password);
			}
			$usuario->save();

			$collection = Restriccion::where('id_usuario', $request->idUsuario)->get(['id']);
			Restriccion::destroy($collection->toArray());

			if(isset($request->unidades)){
				$unidades = $request->unidades;
				foreach($unidades as $unidad){
					$res = new Restriccion;
					$res->id_usuario = $usuario->id;
					$res->id_unidad = $unidad;
					$res->fecha = Carbon::now()->format('Y-m-d H:i:s');
					$res->save();
				}
			}

			if($request->email != "" && $request->password != ""){
				$r = $request->password;
				$correo = $request->email;
				$this->correoClaveUsuarioModificada($usuario, $r, $correo);
			}
			DB::commit();
			return response()->json(["exito" => "El usuario ha sido modificado"]);
		}catch(Exception $ex){
			Log::info($ex);
			DB::rollback();
			return response()->json(["error" => "Error al modificar el usuario", "msg" => $ex->getMessage()]);
		}
	}
	public function cargarRestricciones($idUsuario){
		$restricciones = Restriccion::where("id_usuario", $idUsuario)->get();
		$restriccionesArray = [];

		foreach($restricciones as $res){
			$restriccionesArray []= $res["id_unidad"];
		}

		return $restriccionesArray;
	}

	public function editarUsuario($idUsuario){
		
		$establecimientos = Establecimiento::getEstablecimientos(false);
		$tipoUsuario=Consultas::getTipoUsuario();
		$usuario = Usuario::find($idUsuario);
		$especialidad=Especialidades::getEspecialidades();
		$unidades_en_establecimiento = UnidadEnEstablecimiento::where("establecimiento", Auth::user()->establecimiento)->get();

		$unidades = [];
		foreach($unidades_en_establecimiento as $u){
			$unidades [$u->id] = $u->alias;
		}

		$restricciones = $this->cargarRestricciones($idUsuario);
		
		if($usuario->dv == 10){ 
			$usuario->dv = "K"; 
		} 


		return View::make("Administracion/EditarUsuario", [
			"tipoUsuario" => $tipoUsuario, 
			"establecimiento" => $establecimientos, 
			"usuario" => $usuario,
			"unidades" => $unidades,
			"restricciones" => $restricciones,
			"especialidad" => $especialidad,
		]);
	}

	

	public function formUsuario(){
		$tipoUsuario=Consultas::getTipoUsuario();
		$establecimiento=Establecimiento::getEstablecimientosSinTodos();
		$usuarios=Usuario::getUsuarios();
		$usuariosDeshabilitados=Usuario::getUsuariosDeshabilitados();
		$especialidad= Especialidades::getEspecialidades();
		return View::make("Administracion/CrearUsuario", [
			"tipoUsuario" => $tipoUsuario, 
			"establecimiento" => $establecimiento, 
			"usuarios" => $usuarios,
			"usuariosDeshabilitados" => $usuariosDeshabilitados,
			"especialidad" => $especialidad
		]);
	}

	public function registrarUsuario(Request $request){
		try{

			$rut=trim($request->input("rut"));
			$dv=trim($request->input("dv"));
			$dv=(strtolower($dv) == "k") ? 10 : $dv;

			//$password=trim($request->input("password"));
			$r = Str::random(16);
			$password = $r;
			$tipo=$request->input("tipoUsuario");
			$establecimiento=$request->input("establecimiento");
			$nombres = trim($request->input("nombre"));
			$apellido_paterno = trim($request->input("apellido_paterno"));
			$apellido_materno = trim($request->input("apellido_materno"));
			$especialidad = $request->input("especialidad");
			
			$gestor_iaas=$request->input("gestor_iaas");
			
			if ($gestor_iaas=='Si')$gestor_iaas='TRUE';
        	else $gestor_iaas='FALSE';

			if(
				$rut === '' || $rut === null ||
				$dv === '' || $dv === null ||
				$password === '' || $password === null ||
				$tipo === '' || $tipo === null ||
				$establecimiento === '' || $establecimiento === null ||
				$apellido_paterno === '' || $apellido_paterno === null ||
				$nombres === '' || $nombres === null 
			){
				throw new Exception("Faltan datos");
			}

			if(Usuario::estaBorrado($rut)){
				$usuario=Usuario::where("rut", "=", $rut)->first();
				$usuario->rut=$rut;
				$usuario->dv=$dv;
				$usuario->tipo=$tipo;
				$usuario->password=Hash::make($password);
				$usuario->email = $request->input("email");
				$usuario->nombres = $nombres;
				$usuario->apellido_paterno = $apellido_paterno;
				$usuario->apellido_materno = $apellido_materno;
				$usuario->visible=true;
				$usuario->fecha_ingreso = now()->format("Y-m-d H:i:s");
				$usuario->iaas=$gestor_iaas;
				if($tipo != "admin_ss" && $tipo != "monitoreo_ssvq" && $tipo != "admin_iaas") $usuario->establecimiento=$establecimiento;
				if($tipo == "secretaria") $usuario->usuario_especialidad=$especialidad;
				$usuario->save();
				$this->correoClaveUsuarioCreado($usuario, $r);

				return response()->json(["exito" => "El usuario ha sido registrado"]);
			}

			if(Usuario::existeUsuario($rut)) return response()->json(["error" => "El usuario ya se encuentra registrado"]);

			$usuario=new Usuario;
			$usuario->rut=$rut;
			$usuario->dv=$dv;
			$usuario->tipo=$tipo;
			$usuario->password=Hash::make($password);
			$usuario->email = $request->input("email");
			$usuario->nombres = $nombres;
			$usuario->apellido_paterno = $apellido_paterno;
			$usuario->apellido_materno = $apellido_materno;
			$usuario->visible=true;
			$usuario->fecha_ingreso = now()->format("Y-m-d H:i:s");
			$usuario->iaas=$gestor_iaas;
			if($tipo != "admin_ss" && $tipo != "admin_iaas") $usuario->establecimiento=$establecimiento;
			if($tipo == "secretaria") $usuario->usuario_especialidad=$especialidad;
			$usuario->save();
			$this->correoClaveUsuarioCreado($usuario, $r);

			return response()->json(["exito" => "El usuario ha sido registrado"]);

		}catch(Exception $ex){
			return response()->json(["error" => "Error al registrar el usuario", "msg" => $ex->getMessage()]);
		}
	}

	public function correoClaveUsuarioCreado($usuario, $r){

		$subject = "El Usuario ha sido creado correctamente";
		$for = $usuario->email;

		Mail::send('emails.UsuarioCreado', ['usuario' => $usuario, 'clave' => $r], function($msj) use ($subject,$for){
			$msj->from("soporte.sigicam@uv.cl", "SIGICAM");
			$msj->subject($subject);
			$msj->to($for);
		});
	}

	public function borrarUsuario(Request $request){
		try{
			$id=$request->input("id");
			$usuario=Usuario::find($id);
			$usuario->visible=false;
			$usuario->establecimiento = null;
			$usuario->save();

			return response()->json(["exito" => "El usuario ha sido borrado"]);
		}catch(Exception $ex){
			return response()->json(["error" => "Error al borrar el usuario", "msg" => $ex->getMessage()]);
		}
	}

	public function deshabilitarUsuario(Request $request){
		try {
			DB::beginTransaction();
			$deshabilitar = Usuario::findOrFail($request->id);
			$deshabilitar->visible = false;
			$deshabilitar->save();
			DB::commit();
			return response()->json(["exito" => "Usuario deshabilitado exitosamente."]);
		} catch (Exception $ex) {
			DB::rollback();
			return response()->json(["Error" => "No se ha podido deshabilitar el usuario.", "msg" => $ex]);
		}
	}

	public function habilitarUsuario(Request $request){
		try {
			DB::beginTransaction();
			$habilitar = Usuario::findOrFail($request->id);
			$habilitar->visible = true;
			$habilitar->save();
			DB::commit();
			return response()->json(["exito" => "Usuario habilitado exitosamente."]);
		} catch (Exception $ex) {
			Log::info($ex);
			DB::rollback();
			return response()->json(["Error" => "No se ha podido habilitar el usuario.", "msg" => $ex]);
		}
	}

	public function validarRut(Request $request){
		try{

			$rut = $request->input("rut");
			$usuario = Usuario::where("rut", $rut)->where("visible", true)->first();
			if ($usuario){
				return response()->json(["valid" => false, "message" => "El run pertenece a un usuario activo" ]);
			}
		return response()->json(["valid" => true]);

		}
		catch(Exception $e){
			return "OK";
		}
		
	}

	public function obtenerDatosUsuario(Request $request){
		$rut = $request->input("rut");
		$usuario = Usuario::where("rut", $rut)->first();
		if($usuario){
			return response()->json(["exito" => true, "usuario" => $usuario->toArray()]);
		}
		return response()->json(["exito" => false]);
	}

	public function cambiarContraseña(Request $request){
		$rut = $request->input("rut");
		$vieja = $request->input("vieja");
		$nueva = $request->input("nueva");
		$r = $nueva;
		$correo = $request->input("email");
		$hoy = now()->format("Y-m-d H:i:s");
		if(Auth::attempt(["rut" => $rut, "password" => $vieja])){
			$user = Usuario::where("rut", $rut)->where("visible", true)->firstOrFail();	
			if($correo != "" || $correo != null){
				$user->password = Hash::make($nueva);
				$user->updated_at = $hoy;
				$user->email = $correo;
				$user->save();
				$this->correoClaveUsuarioModificada($usuario, $r, $correo);
			}else{
				$user->password = Hash::make($nueva);
				$user->updated_at = $hoy;
				$user->save();
			}
			return response()->json(["exito" => true, "mensaje" => "Contraseña cambiada exitosamente"]);

		}
		return response()->json(["exito" => false, "mensaje" => "Contraseña actual no corresponde"]);
	}

	public function cambiarPassword(Request $request){
		$password=trim($request->input("passwordNew"));
		$correo=trim($request->input("correo"));
		$r = $password;
		$id=Auth::user()->id;
		$usuario=Usuario::find($id);
		$hoy = now()->format("Y-m-d H:i:s");
		$usuario->password=Hash::make($password);
		$usuario->updated_at = $hoy;
		$usuario->save();
		$this->correoClaveUsuarioModificada($usuario, $r, $correo);
		return response()->json(["exito" => true, "mensaje" => "Contraseña cambiada exitosamente"]);
	}

	public function correoClaveUsuarioModificada($usuario, $r, $correo){

		$subject = "Su contraseña ha sido cambiada correctamente";
		$for = $correo;

		Mail::send('emails.UsuarioModificado', ['usuario' => $usuario, 'clave' => $r], function($msj) use ($subject,$for){
			$msj->from("soporte.sigicam@uv.cl", "SIGICAM");
			$msj->subject($subject);
			$msj->to($for);
		});
	}

	public function mismaPassword(Request $request){
		$password=$request->input("password");
		$igual=Hash::check($password, Auth::user()->password);
		return response()->json(["valid" => $igual]);
	}

	public function validar_establecimiento(Request $request){
        
        $cuidados_validos = Establecimiento::orderBy("nombre","asc")->pluck('id');
        $validador = Validator::make($request->all(), [
            'establecimiento' => Rule::in($cuidados_validos)
        ]);

        if($validador->fails()){
            return response()->json(["valid" => false, "message" => "Debe seleccionar un establecimiento existente"]);
        }else{
            return response()->json(["valid" => true]);
        }
    }
	public function validar_especialidades(Request $request){
        
        $especialidades_validos = Especialidades::orderBy("nombre","asc")->pluck('id');
        $validador = Validator::make($request->all(), [
            'especialidad' => Rule::in($especialidades_validos)
        ]);

        if($validador->fails()){
            return response()->json(["valid" => false, "message" => "Debe seleccionar una especialidad existente"]);
        }else{
            return response()->json(["valid" => true]);
        }
    }

}

?>