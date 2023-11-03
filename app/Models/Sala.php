<?php

namespace App\Models;
use App\Models\HistorialOcupacion;
use App\Models\HistorialBloqueo;
use App\Models\Cama;
use Illuminate\Database\Eloquent\Model;
use View;
use DB;
use Log;
use Carbon\Carbon;
class Sala extends Model{
	protected $table = "salas";
	public function camas(){
		return $this->hasMany("App\Models\Cama", "sala", "id");
	}

	public function camasBloqueadas(){
		return $this->camas()->bloqueadas();
	}

	public function camasReservadas(){
		return $this->camas()->reservadas();
	}

	public function camasLibres(){
		return $this->camas()->libres();

	}

	public function camasOcupadas(){
		return $this->camas()->ocupadas();
	}

	public function camasHabilitadas(){
		return $this->camas()->habilitadas();
	}

	public function camasVigentes(){
		return $this->camas()->vigentes();
	}

	public function camasReconvertidas(){
		return $this->camas()->reconvertidas();
	}


	public function unidadEnEstablecimiento(){
		return $this->belongsTo("App\Models\UnidadEnEstablecimiento", "establecimiento", "id");

	}

	public function unidad(){
		return Unidad::findOrFail( $this->unidadEnEstablecimiento->unidad );
	}

	public function establecimiento(){
		return Establecimiento::findOrFail( $this->unidadEnEstablecimiento->establecimiento );	
	}

	public function bloquearCama($cama, $motivo){
		return Cama::findOrFail($cama)->bloquear($motivo);
	}

	public function agregarCama($tipo = null, $nombre = null, $descr = null){
		$cama = new Cama();
		$cama->sala = $this->id;
		$cama->tipo = $tipo;
		$cama->descripcion = $descr;
		$cama->save();
		return $cama;
	}

	public function agregarCamas($cantidad = 1, $tipo = null){
		$arr = array();
		foreach( range( 1, $cantidad ) as $i ){
			$arr[] = $this->agregarCama($tipo);
		}
		return new \Illuminate\Database\Eloquent\Collection($arr);
	}
	
	public function eliminar(){
		/*$eliminacion = new HistorialEliminacion();
		$eliminacion->cama = */
	}

	public static function getSalasEstablecimiento($idEstablecimiento){
		$response=array();
		$salas=self::where("establecimiento", "=", $idEstablecimiento)->orderBy("nombre")->get();
		foreach($salas as $sala) $response[]=[
			"id" => $sala->id,
			"idSala" => $sala->id_sala,
			"nombre" => $sala->nombre, 
			"editar" => View::make("Administracion/OpcionBorrar", ["nombre" => "editarSala", "id" => $sala->id, "sala" => $sala->nombre])->render()
		];
		return $response;
	}

	public static function obtenerSalasEstablecimientoPorUnidad($idEstab, $idUnidad){
		$salas=DB::table("establecimientos as e")->join("salas as s", "e.id", "=", "s.establecimiento")
		->join("unidades_en_establecimientos as u", "u.establecimiento", "=", "e.id")
		->where("u.establecimiento", "=", $idEstab)
			->where("u.id", "=", $idUnidad)
			->select("s.id", "s.nombre")
			->orderBy("s.nombre")
			->get();
		return $salas;
	}

	public static function getSalasEstablecimientoSelect($idEstablecimiento){
		$response=array();
		$salas=self::where("establecimiento", "=", $idEstablecimiento)->orderBy("nombre")->get();
		foreach($salas as $sala) $response[$sala->id]=$sala->nombre;
		return $response;
	}

	public static function idSalaUnico($idSala, $establecimiento){
		$sala=self::where("id_sala", "=", $idSala)->where("establecimiento", "=", $establecimiento)->first();
		if(is_null($sala)) return true;
		return false;
	}

	public static function tieneCamasOcupadasOBloqueadas($idSala){
		$queryCamas =
    	DB::select(
			DB::raw("
			select camas.id 
			from 
			camas
			left join salas on salas.id = camas.sala 
			where  
			not exists 
			(select * from historial_eliminacion_camas where camas.id = historial_eliminacion_camas.cama and fecha < '".Carbon::now()->format('Y-m-d H:i:s')."') 
			and sala = '".$idSala."'
			and salas.visible = true;
			")
   		 );

		if(!empty($queryCamas)){
			$camas = [];
			foreach($queryCamas as $cama){
				$camas[]=$cama->id;
			}
			$camasOcupadas = HistorialOcupacion::estaOcupada($camas);
			$camasBloqueadas = HistorialBloqueo::estaBloqueada($camas);
			if($camasOcupadas == 0 && $camasBloqueadas == 0)
					return true;
				return false;
		}else{
			return 'vacio';
		}
	}


}
