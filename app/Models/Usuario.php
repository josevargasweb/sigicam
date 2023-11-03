<?php

//use Illuminate\Auth\UserTrait;
//use Illuminate\Auth\UserInterface;
//use Illuminate\Auth\Reminders\RemindableTrait;
//use Illuminate\Auth\Reminders\RemindableInterface;
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Auth;
use DB;
use TipoUsuario;
use App\Models\Feriado;
use Carbon\Carbon;

class Usuario extends Model implements AuthenticatableContract
{
	

	protected $table = "usuarios";
	protected $fillable = array('rut', 'password', 'establecimiento');
	public $timestamps = false;



 public function getAuthIdentifierName(){
 	return 'id';
 }


		/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	public function getRememberToken(){
		 return $this->remember_token; 
	}


public function setRememberToken($value)
{
    $this->remember_token = $value;
}

public function getRememberTokenName()
{
    return 'remember_token';
}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function establecimiento(){
		return $this->belongsTo("App\Models\Establecimiento", "establecimiento", "id");
	}
	public function establecimientoUsuario(){
		return $this->belongsTo("App\Models\Establecimiento", "establecimiento", "id");
	}

	public function getNombreEstablecimiento(){
		$estab=$this->belongsTo("App\Models\Establecimiento");
		if(!is_null($estab->first())) return $estab->nombre;
		return "";
	}

	public function contingencias(){
		return $this->hasMany("App\Models\SolicitudContingencia", "usuario", "id");
	}

	public static function existeUsuario($rut){
		$usuario=self::where("rut", "=", $rut)->first();
		if($usuario != null) return true;
		return false;
	}

	public static function estaBorrado($rut){
		$usuario=self::where("rut", "=", $rut)->where("visible", "=", false)->first();
		if($usuario != null) return true;
		return false;
	}

	public static function horarioInhabil(){
		
		$hoy = Carbon::now();
		$hora = $hoy->format("H"); 

		$es_Feriado = Feriado::where("fecha", $hoy)->first();

		$dia = $hoy->format("l");
 
		if($dia == "Sunday" || $dia == "Saturday" || !is_null($es_Feriado) || ($dia == "Friday" && $hora>16)){
			return true;
		}else{
			return false;
		}
	}

	public static function horaInhabil(){
		
		$hoy = Carbon::now();
		
		return $hoy->format("H");
	
		if($dia == "Sunday" || $dia == "Saturday" || !is_null($es_Feriado) ){
			return true;
		}else{
			return false;
		}

	}

 

	public static function getUsuarios(){
		$response=array();
		$actual=Auth::user()->rut;
		$usuarios=DB::table("usuarios as u")->join("establecimientos as e", "e.id", "=", "u.establecimiento")
		->where("u.visible", "=", true)->where("u.tipo", "!=", "admin_ss")->where("rut", "!=", $actual)
		->select("u.id", "u.rut", "u.dv", "u.tipo", "e.nombre", "u.nombres", "u.apellido_paterno", "u.apellido_materno", "u.email")
		->get();
		foreach($usuarios as $usuario){
			$dv=($usuario->dv == 10) ? "K" : $usuario->dv;
			$response[]=array("id" => $usuario->id, "tipo" => TipoUsuario::getNombre($usuario->tipo), "rut" => $usuario->rut, "dv" => $dv, "nombre" => $usuario->nombre,
				"nombres" => $usuario->nombres, "apellido_paterno" => $usuario->apellido_paterno, "apellido_materno" => $usuario->apellido_materno, "email" => $usuario->email, );
		}
		$usuarios=self::where("tipo", "=", "admin_ss")->where("visible", "=", true)->where("rut", "!=", $actual)
		->select("id", "rut", "dv", "tipo", "nombres", "apellido_paterno", "apellido_materno", "email")
		->get();
		foreach($usuarios as $usuario){
			$dv=($usuario->dv == 10) ? "K" : $usuario->dv;
			$response[]=array("id" => $usuario->id, "tipo" => TipoUsuario::getNombre($usuario->tipo), "rut" => $usuario->rut, "dv" => $dv, "nombre" => "",
				"nombres" => $usuario->nombres, "apellido_paterno" => $usuario->apellido_paterno, "apellido_materno" => $usuario->apellido_materno, "email" => $usuario->email, );
		}
		return $response;
	}

	public static function getUsuariosDeshabilitados(){
		$response=array();
		$actual=Auth::user()->rut;
		$usuarios=DB::table("usuarios as u")->join("establecimientos as e", "e.id", "=", "u.establecimiento")
		->where("u.visible", "=", false)->where("u.tipo", "!=", "admin_ss")->where("rut", "!=", $actual)
		->select("u.id", "u.rut", "u.dv", "u.tipo", "e.nombre", "u.nombres", "u.apellido_paterno", "u.apellido_materno", "u.email")
		->get();
		foreach($usuarios as $usuario){
			$dv=($usuario->dv == 10) ? "K" : $usuario->dv;
			$response[]=array("id" => $usuario->id, "tipo" => TipoUsuario::getNombre($usuario->tipo), "rut" => $usuario->rut, "dv" => $dv, "nombre" => $usuario->nombre,
				"nombres" => $usuario->nombres, "apellido_paterno" => $usuario->apellido_paterno, "apellido_materno" => $usuario->apellido_materno, "email" => $usuario->email, );
		}
		$usuarios=self::where("tipo", "=", "admin_ss")->where("visible", "=", false)->where("rut", "!=", $actual)
		->select("id", "rut", "dv", "tipo", "nombres", "apellido_paterno", "apellido_materno", "email")
		->get();
		foreach($usuarios as $usuario){
			$dv=($usuario->dv == 10) ? "K" : $usuario->dv;
			$response[]=array("id" => $usuario->id, "tipo" => TipoUsuario::getNombre($usuario->tipo), "rut" => $usuario->rut, "dv" => $dv, "nombre" => "",
				"nombres" => $usuario->nombres, "apellido_paterno" => $usuario->apellido_paterno, "apellido_materno" => $usuario->apellido_materno, "email" => $usuario->email, );
		}
		return $response;
	}
}

?>