<?php
namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use DB;
use Log;
use Session;
//use App\Models
use Exception;
use TipoUsuario;
use Carbon\Carbon;
use App\Models\Telefono;

class Paciente extends Model implements Auditable{
 
	use \OwenIt\Auditing\Auditable;

	protected $table = "pacientes";
	protected $fillable = ["n_identificacion"];
	protected $auditInclude = [
       
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;
 
 
	public static function boot(){
		parent::boot();

		self::saving(function($ev){
			if($ev->dv === 'k' || $ev->dv === 'K'){
				$ev->dv = 10;
			}
		});

        self::updating(function($pac){
			$original = $pac->getOriginal();
            foreach($original as $campo => $val){
                if(trim($pac->$campo) === '' && gettype($pac->$campo) !== 'boolean') $pac->$campo = $val;
			}
		
			
        });
	}

	public function idsEnEstablecimientos(){
		return $this->hasMany("App\Models\IDPaciente", "paciente", "id");
	}

	public static function formatearRut($rut, $dv){
		if(!empty($rut) && !empty($dv)){
			$dv = $dv % 11;
			$dv = $dv == 10? 'K' : $dv;
			return "{$rut}-{$dv}";
		}else{
			if($dv == 0){
				return "{$rut}-{$dv}";
			}else{
				return "-";
			}
		}
	}

	public function getRutFormateado(){
		return self::formatearRut($this->rut, $this->dv);
	}

	public static function edad($fecha_nacimiento){
		
        try{
        	if($fecha_nacimiento != null){
        		$f_nac = \Carbon\Carbon::createFromFormat("Y-m-d", $fecha_nacimiento);
        		return \Carbon\Carbon::now()->diffInYears($f_nac);
        	}
        	else
        	{
        		return "No disponible";
        	}

        }
        catch(Exception $e){
            return "No disponible";
        }

	}

	public static function getDatosPaciente($rut){
		return DB::table('ultimos_estados_pacientes as u')
		->join('casos as c', 'u.caso', '=', 'c.id')
		->join('pacientes as p', 'p.id', '=', 'c.paciente')
		->select('alias', 'dv', 'sexo', 'fecha_nacimiento', 'diagnostico', 'riesgo')
		->where('p.rut', '=', $rut)->first();
	}

	public static function getRutPorCaso($idCaso){
		try{
			return self::getPacientePorCaso($idCaso)->rut;
		}
		catch(Exception $e){
			return false;
		}
	}

	public static function getPacientePorCaso($idCaso){
		return Caso::find($idCaso)->paciente()->leftJoin('comuna', 'pacientes.id_comuna', '=','comuna.id_comuna')->firstOrFail();
	}

	public static function getIDCasoPaciente($rut, Exception $e){

		try{


			$ret = DB::table('ultimos_estados_pacientes as u')
			->join('casos as c', 'u.caso', '=', 'c.id')
			->join('pacientes as p', 'p.id', '=', 'c.paciente')
			->select('c.id')->where('p.rut', '=', $rut)->first();


			//return new \Illuminate\Database\Eloquent\ModelNotFoundException;
			if($ret){
				return $ret->id;
			}
			else{

				//throw (new \Illuminate\Database\Eloquent\ModelNotFoundException)->setModel(get_class());
				throw new \Illuminate\Database\Eloquent\ModelNotFoundException;

			}

		}
		catch(Exception $e){
			return $e;
		}
	}

	public static function getIDCasoPorIDPaciente($id){
		return DB::table("casos as c")->where("paciente", "=", $id)->first()->id;
	}

	public static function existePaciente($rut){
		$paciente=self::where("rut", "=", $rut)->first();
		if(!is_null($paciente)) return true;
		return false;
	}

	public static function getIdPaciente($rut){
		return self::where("rut", "=", $rut)->first()->id;
	}

	public function registrarPaciente($paciente){
		/* return "esto esta bueno"; */
		return DB::transaction(function() use ($paciente){
			$this->registrarNuevoPaciente($paciente);
			
			//aqui continua 3
			$caso = $this->registrarCasoPaciente($paciente);
			/* return $caso; */
			$caso->establecimiento = Session::get("idEstablecimiento");
			/* $caso->save(); */
			return $caso;
		});
	}

	public function registrarNuevoPaciente($paciente){
		$rut = strip_tags(trim($paciente["rut"]));
		$dv= strip_tags(strtoupper(trim($paciente["dv"])));
		$this->rut= $rut == ''? null:$rut;
		if($this->rut) {
			if ($dv == "K") $dv = 10;
			$this->dv = $dv;
		}
		else{
			$this->dv = null;
		}
		if(isset($paciente["sexo"])) $this->sexo= strip_tags(trim($paciente["sexo"]));

		if($paciente["fechaNac"] == ""){
			$this->fecha_nacimiento=null;
		}else{
			$this->fecha_nacimiento= date("Y-m-d", strip_tags(strtotime(trim($paciente["fechaNac"]))));
		}
		if($paciente["extranjero"] == "si"){
			$this->extranjero=true;
			$this->n_identificacion = ($paciente["n_pasaporte"]) ? strip_tags($paciente["n_pasaporte"]) : null;
			$this->identificacion = 'pasaporte';
		}else{
			$this->extranjero=false;
		}
		
		$this->nombre= trim($paciente["nombre"]);
		$this->apellido_paterno= trim($paciente["apellidoP"]);
		$this->apellido_materno= trim($paciente["apellidoM"]);
		//$this->nombre_social=trim($paciente["nombreSocial"]);
		$this->nombre_social= $paciente["nombreSocial"] == ''? null:$paciente["nombreSocial"];
		$this->calle = $paciente["calle"];
		
		if($paciente["numeroCalle"] != null){
			$this->numero = $paciente["numeroCalle"];
		}
		if($paciente["latitud"] != null){
			$this->latitud = $paciente["latitud"];
		}
		if($paciente["longitud"] != null){
			$this->longitud = $paciente["longitud"];
		}
		
		
		$this->observacion = $paciente["observacionCalle"];
		
		

		$this->id_comuna = $paciente["comuna"];

		if($paciente["rango"] == "seleccione"){
			$this->rango_fecha = null;
		}else{
			$this->rango_fecha = $paciente["rango"];
		}

		$this->rn = $paciente["rn"];
		if($this->rn == "si"){
			if($paciente["rutMadre"] && $paciente["dvMadre"]){
				$this->rut_madre = $paciente["rutMadre"];
				$this->dv_madre = ($paciente["dvMadre"] == "K" || $paciente["dvMadre"] == "k")?10:$paciente["dvMadre"];
			}else{
				$this->rut_madre = null;
				$this->dv_madre = null;
			}
		}

		if(isset($paciente["puebloind"]) && $paciente["puebloind"] == "si"){
			$pueblo = $paciente["pueblo_ind"];
			$this->pueblo_indigena = $pueblo;
			if($pueblo == 'Otro'){
				$this->detalle_pueblo_indigena = $paciente["esp_pueblo"];
			}else if($pueblo == null){
				$this->detalle_pueblo_indigena = null;
			}
		}
		
		$this->save();

		$tipo_telefono = $paciente["tipo_telefono"];
		$telefono = $paciente["telefono"];
		// $this->telefono = $paciente["telefono"];
		// if($telefono[0] != ''){
		foreach ($tipo_telefono as $key => $tipo) {
			if($telefono[$key] != null){
				$nuevo_telefono = new Telefono;
			$nuevo_telefono->id_paciente = $this->id;
			$nuevo_telefono->tipo = $tipo;
			$nuevo_telefono->telefono = $telefono[$key];
			$nuevo_telefono->save();
			}
		}
		// }
		return $this->id;
	}

	public function registrarCasoPaciente($paciente){
		$caso=new Caso;
		$caso->paciente=$this->id;
		
		if(isset($paciente["prevision"])){
			$caso->prevision = $paciente["prevision"];
		} 
		$caso->fecha_ingreso=DB::raw("date_trunc('seconds', now())");
		if(isset($paciente["medico"])){
			$caso->medico=trim($paciente["medico"]);	
		} 
		$caso->save();
		if(isset($paciente["riesgo"])){
			$this->registrarEvolucionPacienteNoInput($caso->id, $paciente);
		} 
		
		return $caso;
	}

	public static function registrarCasoPacienteHospDom($paciente){
		$caso=new Caso;
		$caso->paciente=$paciente["id"];

		if(isset($paciente["prevision"])){
			$caso->prevision = $paciente["prevision"];
		} 
		$caso->fecha_ingreso=DB::raw("date_trunc('seconds', now())");
		if(isset($paciente["medico"])){
			$caso->medico=trim($paciente["medico"]);
		} 
		$caso->save();
		if(isset($paciente["riesgo"])){
			Paciente::registrarEvolucionPacienteNoInput($caso->id, $paciente);
		} 
		
		return $caso;
	}
	/*Se creo porque no se detectan los input */
	public static function registrarEvolucionPacienteNoInput($idCaso, $riesgo){
		//aqui quede en 
		if( ($riesgo["riesgo"] === '') && (is_null($riesgo["riesgo"]) || $riesgo["riesgo"] === 0) && ($riesgo["riesgo"] === '0') && ($riesgo["riesgo"] != 'No disponible') ) {
			return;
		};

		if (($riesgo["riesgo"] != null) && ($riesgo["riesgo"] != "No disponible" ) && ($riesgo["riesgo"] != "" ) ) {
			

			if($riesgo["servicios"] == '195' || $riesgo["servicios"] == '196'){
				$nuevoriesgo= new Riesgo;
				$nuevoriesgo->dependencia1 = $riesgo["dependencia1_2"];
				$nuevoriesgo->dependencia2 = $riesgo["dependencia2_2"];
				$nuevoriesgo->dependencia3 = $riesgo["dependencia3_2"];
				$nuevoriesgo->dependencia4 = $riesgo["dependencia4_2"];
				$nuevoriesgo->dependencia5 = $riesgo["dependencia5_2"];
				$nuevoriesgo->riesgo1 = $riesgo["riesgo1_2"];
				$nuevoriesgo->riesgo2 = $riesgo["riesgo2_2"];
				$nuevoriesgo->riesgo3 = $riesgo["riesgo3_2"];
				$nuevoriesgo->riesgo4 = $riesgo["riesgo4_2"];
				$nuevoriesgo->riesgo5 = $riesgo["riesgo5_2"];
				$nuevoriesgo->riesgo6 = $riesgo["riesgo6_2"];
				$nuevoriesgo->riesgo7 = $riesgo["riesgo7_2"];
				$nuevoriesgo->riesgo8 = $riesgo["riesgo8_2"];
				$nuevoriesgo->riesgo9 = $riesgo["riesgo9_2"];
				$nuevoriesgo->categoria = $riesgo["riesgo"];
				$nuevoriesgo->save();
			
			}
			else{
				$nuevoriesgo= new Riesgo;
				$nuevoriesgo->dependencia1 = $riesgo["dependencia1"];
				$nuevoriesgo->dependencia2 = $riesgo["dependencia2"];
				$nuevoriesgo->dependencia3 = $riesgo["dependencia3"];
				$nuevoriesgo->dependencia4 = $riesgo["dependencia4"];
				$nuevoriesgo->dependencia5 = $riesgo["dependencia5"];
				$nuevoriesgo->dependencia6 = $riesgo["dependencia6"];
				$nuevoriesgo->riesgo1 = $riesgo["riesgo1"];
				$nuevoriesgo->riesgo2 = $riesgo["riesgo2"];
				$nuevoriesgo->riesgo3 = $riesgo["riesgo3"];
				$nuevoriesgo->riesgo4 = $riesgo["riesgo4"];
				$nuevoriesgo->riesgo5 = $riesgo["riesgo5"];
				$nuevoriesgo->riesgo6 = $riesgo["riesgo6"];
				$nuevoriesgo->riesgo7 = $riesgo["riesgo7"];
				$nuevoriesgo->riesgo8 = $riesgo["riesgo8"];
				$nuevoriesgo->categoria = $riesgo["riesgo"];
				$nuevoriesgo->save();
			}

			$id_riesgo = $nuevoriesgo->id;
		}else{
			$id_riesgo = null;
		}
		
		$riesgo_comentario = $riesgo["comentario-riesgo"];
		$riesgo = $riesgo["riesgo"];
		try{
			$actual = EvolucionCaso::where("caso", $idCaso)
				->orderBy("fecha", "desc")
				->firstOrFail();
			if($actual->riesgo == $riesgo){
				throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
			}
		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			$evolucion = new EvolucionCaso;
			$evolucion->caso = $idCaso;
			$evolucion->riesgo = ($riesgo == 0) ? null : $riesgo;
			$evolucion->riesgo_id = $id_riesgo;
			$evolucion->fecha = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
			$evolucion->id_usuario = Session::get('usuario')->id;
			$evolucion->comentario = $riesgo_comentario;
			if(Session::get("usuario")->tipo == TipoUsuario::USUARIO){
				$evolucion->urgencia = true;
			}
			else{
				$evolucion->urgencia = false;
			}
			$evolucion->save();
		}
	}

	public function registrarEvolucionPaciente($idCaso, $riesgo){
		//aqui quede en 

		if (($riesgo != null) && ($riesgo != "No disponible" ) && ($riesgo != "" ) ) {
			$nuevoriesgo= new Riesgo;
			$nuevoriesgo->dependencia1 = $riesgo->dependencia1;
			$nuevoriesgo->dependencia2 = $riesgo->dependencia2;
			$nuevoriesgo->dependencia3 = $riesgo->dependencia3;
			$nuevoriesgo->dependencia4 = $riesgo->dependencia4;
			$nuevoriesgo->dependencia5 = $riesgo->dependencia5;
			$nuevoriesgo->dependencia6 = $riesgo->dependencia6;
			$nuevoriesgo->riesgo1 = $riesgo->riesgo1;
			$nuevoriesgo->riesgo2 = $riesgo->riesgo2;
			$nuevoriesgo->riesgo3 = $riesgo->riesgo3;
			$nuevoriesgo->riesgo4 = $riesgo->riesgo4;
			$nuevoriesgo->riesgo5 = $riesgo->riesgo5;
			$nuevoriesgo->riesgo6 = $riesgo->riesgo6;
			$nuevoriesgo->riesgo7 = $riesgo->riesgo7;
			$nuevoriesgo->riesgo8 = $riesgo->riesgo8;
			$nuevoriesgo->categoria = $riesgo->input("riesgo");
			$nuevoriesgo->save();

			$id_riesgo = $nuevoriesgo->id;
		}else{
			$id_riesgo = null;
		}
		
		try{
			$actual = EvolucionCaso::where("caso", $idCaso)
				->orderBy("fecha", "desc")
				->firstOrFail();
			if($actual->riesgo == $riesgo){
				throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
			}
		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

			$evolucion = new EvolucionCaso;
			$evolucion->caso = $idCaso;
			$evolucion->riesgo = ($riesgo == 0) ? null : $riesgo;
			$evolucion->riesgo_id = $id_riesgo;
			$evolucion->fecha = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
			$evolucion->save();
		}
	}

	public function derivar($idEstablecimientoDestino){
		return DB::transaction( function() use($idEstablecimientoDestino) {
			$h = HistorialOcupacion::getPorPaciente($this->id);
			if (!$h->isEmpty()){
				$caso = Caso::find($h->caso);
				$caso->liberar();
			}
			else{
				$caso = new Caso();
				$caso->asignarPaciente($this->id);
			}
			return $caso->derivar($idEstablecimientoDestino);
		});
	}

	public function casos(){
		return $this->hasMany('App\Models\Caso', 'paciente', 'id')->orderBy("fecha_ingreso", "desc");
	}

	public function casosPaciente(){
		return $this->hasMany('App\Models\Caso', 'paciente', 'id');
	}

	public function casoActual(\Carbon\Carbon $fecha = null){
		if($fecha === null){
			$fecha = \Carbon\Carbon::now();
		}
		return $this->hasMany('App\Models\Caso', 'paciente', 'id')->enFecha($fecha)->orderBy("fecha_ingreso", "desc");
	}

	public function ultimaOcupacion(){
		return DB::table("historial_ocupaciones as ho")
		->where("paciente", "=", $this->id)
		->where("rk", "=", 1)
		->orderBy("fecha", "desc");
	}

	public function scopeSimilar($q, $texto){

		//return "w";
		//return Paciente::where("rut","16677488");

		//NO BORRAR
	/*	return $q->addSelect(
			DB::raw("*, similarity(apellido_paterno , ?)+
			similarity(apellido_materno , ?)+
			similarity(nombre , ?) AS sim"))
		->whereRaw("similarity(apellido_paterno , ?) >= 0.7")
		->orWhereRaw("similarity(apellido_materno , ?) >= 0.7")
		->orWhereRaw("similarity(nombre , ?) >= 0.3")
		->orWhereRaw("similarity(nombre || ' ' || apellido_paterno, ?) >= 0.5")
		->orWhereRaw("similarity(nombre || ' ' || apellido_paterno || ' ' || apellido_materno, ?) >= 0.3")
		->orderBy('sim', 'desc')->setBindings([$texto, $texto, $texto, $texto, $texto, $texto, $texto, $texto]);
*/

		return Paciente::where("nombre",'ilike',"%".$texto."%")
		->orWhere("apellido_paterno","ILIKE","%".$texto."%")
		->orWhere("apellido_materno","ILIKE","%".$texto."%");

	/*	return $q->addSelect(
		DB::raw("*"))
		->orWhereRaw("apellido_paterno ILIKE '%$texto%'")
 		->orWhereRaw("apellido_materno ILIKE '%$texto%'");

*/






	}

	public function scopeRut($q, $rut){
		return $q->whereRut($rut);
	}

	public function tieneCasoActivo(){
		try {
			$est = $this->casoActual()->get();

			if($est->count() == 0){
				throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
			}
			return $est->first()->establecimiento()->first();
		}catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return false;
		}
	}

	public function tieneCasoActivoEnFecha($fecha = null){
		if ($fecha === null){
			return $this->tieneCasoActivo() ? true:false;
		}

		$fecha_ingreso = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $fecha);
		/* @var $caso_inf Caso */
		$caso_inf = $this->casos()->where("fecha_ingreso", "<=", $fecha)->first();
		/* Se selecciona el directamente menor, y se comprueba hasta qué fecha está ocupado */
		if(!is_null($caso_inf) ){
			/* Si la fecha_termino es null entonces está ocupado */
			if(is_null($caso_inf->fecha_termino)){
				return true;
			}

			$fecha_comp = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $caso_inf->fecha_termino);
			if($fecha_comp->gte($fecha_ingreso)){
				/* Si la fecha de termino es mayor que la fecha del nuevo caso entonces el paciente
				si tiene un caso activo */
				return true;
			}
		}
		/* Si el menor no existe no significa nada; hay que comprobar al mayor. */
		/* @var $caso_sup Caso */
		$caso_sup = $this->hasMany("App\Models\Caso", "paciente", "id")
			->orderBy("fecha_ingreso", "asc")
			->where("fecha_ingreso", ">", $fecha)->first();
		/* Si existe mayores, lo siento! no se puede ingresar. */
		if(!is_null($caso_sup)){
			return true;
		}
		return false;
	}

	public function casoActivoEnFecha(\Carbon\Carbon $fecha){
		$fecha_ingreso = Carbon::createFromFormat("Y-m-d H:i:s", $fecha);
		/* @var $caso_inf Caso */
		$caso_inf = $this->casos()->where("fecha_ingreso", "<=", $fecha)->first();
		/* Se selecciona el directamente menor, y se comprueba hasta qué fecha está ocupado */
		if(!is_null($caso_inf) ){
			/* Si la fecha_termino es null entonces está ocupado */
			if(is_null($caso_inf->fecha_termino)){
				return $caso_inf;
			}

			$fecha_comp = Carbon::createFromFormat("Y-m-d H:i:s", $caso_inf->fecha_termino);
			if($fecha_comp->gte($fecha_ingreso)){
				/* Si la fecha de termino es mayor que la fecha del nuevo caso entonces el paciente
				si tiene un caso activo */
				return $caso_inf;
			}
		}
		/* Si el menor no existe no significa nada; hay que comprobar al mayor. */
		/* @var $caso_sup Caso */
		$caso_sup = $this->hasMany("App\Models\Caso", "paciente", "id")
			->orderBy("fecha_ingreso", "asc")
			->where("fecha_ingreso", ">", $fecha)->first();
		/* Si existe mayores, lo siento! no se puede ingresar. */
		if(!is_null($caso_sup)){
			return $caso_sup;
		}
		return null;
	}

	public function puedeIngresarCaso(\Carbon\Carbon $fecha = null){

		if($fecha === null){
			$fecha = \Carbon\Carbon::now();
		}
		$casos_anteriores = $this->casosPaciente()->where("fecha", ">=", "{$fecha}")->orderBy("fecha", "desc")->first();

		if(!is_null($casos_anteriores)){
			if ($casos_anteriores->fecha_termino === null){
				return false;
			}
			$fecha_termino = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $casos_anteriores->fecha_termino);
			if($fecha_termino->gte($fecha)){
				return false;
			}
		}

		$casos_posteriores = $this->casosPacientes()->where("fecha", "<", "{$fecha}")->orderBy("fecha", "asc")->first();
		return $casos_posteriores === null;

	}

	public function insertarCaso(Caso $caso){

		return DB::transaction(function() use ($caso) {

			$fecha = $caso->fecha_ingreso;

			if ($fecha === null) {
				$fecha = Carbon::now();
			}

			/* Se chequean si existen casos en fechas posteriores a la indicada. Obviamente si la fecha
			 * es now(), no debería existir casos futurísticos.
			 * Si la fecha no es now(), podrían encontrarse casos posteriores. Los casos posteriores harían
			 * imposible al gestor de camas el editar estados y cerrar el caso, debido a que sólo puede ver
			 * el más reciente. Si existen casos posteriores, se cancela la inserción con una excepción.
			 */

			$casos_posteriores = $this->casosPaciente()
							->where("fecha_ingreso", ">=", "{$fecha}")
							->orderBy("fecha_ingreso", "asc")
							->first();
							//return response()->json($casos_posteriores);
			//if cuando producia error al ingresar casos del 09-11-2018
			//if(!is_null($casos_posteriores)){

			//nuevo if del 10-11-2018 para reparar 
			if(!is_null($casos_posteriores)){
				if(is_null($casos_posteriores->fecha_termino)){
					/* Si el caso es el mismo? */
					if($casos_posteriores->id == $caso->id && $caso->id !== null){
						
						//return response()->json("if");
						return $casos_posteriores;
	
					}
					/* Si el caso está abierto, es el último (se asume)
					 * Igual no se permite ingresar un *nuevo* caso.
					 */
					/*if($casos_posteriores->fecha_termino === null){
						return $casos_posteriores;
					}*/
					//return response()->json(["entro al primer if" => $casos_posteriores]);
					throw new Exception("El paciente tiene casos posteriores a la fecha indicada");
				}
			}
			
			//return response()->json("paso");
			/* Se chequean los casos en fechas anteriores. Si no hay anteriores da lo mismo; significa que
			 * este caso es el primero. Pero de haber, hay que chequear al más reciente: si el caso está abierto
			 * se cerrará. Si está cerrado, pero la fecha de cierre es posterior, se cambiará esta fecha a una
			 * anterior a la indicada.			 *
			 */

			$casos_anteriores = $this->casosPaciente()->where("fecha_ingreso", "<", "{$fecha}")->orderBy("fecha_ingreso", "desc")->first();

			if (!is_null($casos_anteriores)) {
				if($casos_anteriores->id == $caso->id && $caso->id !== null){
					return $casos_anteriores;
				}
				if ($casos_anteriores->fecha_termino === null) {
					$casos_anteriores->fecha_termino = "{$fecha}";
				} else {
					$fecha_termino = Carbon::createFromFormat("Y-m-d H:i:s", $casos_anteriores->fecha_termino);
					if ($fecha_termino->gte($fecha)) {
						$casos_anteriores->fecha_termino = "{$fecha->copy()->subMinute()}";
					}
				}
			}
			/*
			 * Se crea el nuevo caso con la fecha indicada para el paciente. No se guarda el caso en la BD.
			 * El caso es incompleto, el diagnóstico es desconocido en este punto.
			 */
			$caso->paciente = $this->id;
			return $caso;
		});
	}

	public static function homologarSexo($sexo){
		switch ($sexo) {
			case 'masculino':
				$resp = 'M';
				break;
			case 'femenino':
				$resp = 'F';
				break;
			case 'indefinido':
				$resp = 'I';
				break;
			case 'desconocido':
				$resp = 'D';
				break;
			default:
				$resp = '';
				break;
		}
		return $resp;
	}
}
