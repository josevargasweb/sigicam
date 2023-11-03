<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Establecimiento;
use HTML;
use Consultas;
use View;
use URL;
use App\Models\UnidadEnEstablecimiento;
use App\Models\Sala;
use Funciones;
use App\Models\Cama;
use Auth;
use App\Models\HistorialEliminacion;
use App\Models\HistorialOcupacion;
use App\Models\Unidad;
use App\Models\Comuna;
use DB;
use App\Models\ServicioOfrecido;
use App\Models\ServicioRecibido;

use App\Models\TipoCama;
use App\Models\TipoUnidad;
use Illuminate\Support\Str;
use App\Models\AreaFuncional;
use Log;

use App\Models\Dotacion;
use Carbon\Carbon;

use App\Models\HistorialSubcategoriaUnidad;

class AdministrarUnidadController extends Controller{

	public function unidad($idestab){
		$estab = Establecimiento::findOrFail($idestab);
		$nombre= $estab->nombre;
		$tipo_unidad = TipoUnidad::seleccion();
		$areaFuncional = AreaFuncional::nombreTodasAreasFuncionales();
		return View::make("AdministracionUnidad/Unidad", ["estab" => $estab, "nombre" => $nombre, "idEstab" => $idestab, "tipoUnidad" => $tipo_unidad, "areaFuncional" => $areaFuncional]);
	}

	public function obtenerUnidades($id){
		$unidades=Establecimiento::getUnidadPorEstablecimiento($id);
		$response=array();
		foreach($unidades as $unidad => $value){
			$url = URL::to('/');
			$link = "<a href='$url/administracionUnidad/editarUnidad/$id/$value->id'>Editar</a>";
			$nombre_unidad = Cama::descripcionTipoUnidad($value->tipo_unidad);
			$nombre_unidad = ($nombre_unidad) ? $nombre_unidad : 'Sin Especificar';
			$nombre_area = AreaFuncional::nombreAreaFuncional($value->id_area_funcional);
			$nombre_area = ($nombre_area) ? $nombre_area : 'Sin Especificar';
			$dotacion = Dotacion::dotacion($value->id);
			$dotacion = ($dotacion) ? $dotacion : 'Sin Numero';
			$response[]=array($value->alias, $nombre_unidad, $nombre_area, $dotacion, $link);

		}
		return response()->json(["aaData" => $response]);
	}

	public function editarUnidadView($idEstab, $idUnidad){
		$estab = Establecimiento::findOrFail($idEstab);
		$alias=UnidadEnEstablecimiento::getAliasUnidad($idEstab, $idUnidad);
		$nombre= $estab->nombre;
		$tipo_unidad = TipoUnidad::seleccion();
		$unidad = UnidadEnEstablecimiento::find($idUnidad, ['tipo_unidad','id_area_funcional','cama_temporal']);
		$unidad_seleccionada = ($unidad->tipo_unidad) ? $unidad->tipo_unidad : null;
		$nombre_unidad = Cama::descripcionTipoUnidad($unidad->tipo_unidad);
		$nombre_unidad = ($nombre_unidad) ? $nombre_unidad : 'Sin Especificar';
		$nombre_area = AreaFuncional::nombreAreaFuncional($unidad->id_area_funcional);
		$nombre_area = ($nombre_area) ? $nombre_area : 'Sin Especificar';
		// $areas_funcionales = AreaFuncional::areasEnEstablecimiento($idEstab);
		$areas_funcionales = AreaFuncional::nombreTodasAreasFuncionales();
		$dotacion = Dotacion::dotacion($idUnidad);
		$subCategoriaUnidad = HistorialSubcategoriaUnidad::where('id_unidad',$idUnidad)->where('visible',true)->first();
		return View::make("AdministracionUnidad/EditarUnidad", [
			"estab" => $estab, 
			"alias" => $alias, 
			"nombre" => $nombre, 
			"idEstab" => $idEstab, 
			"idUnidad" => $idUnidad, 
			"tipoUnidad" => $tipo_unidad, 
			"unidadSeleccionada" => $unidad_seleccionada, 
			"nombreUnidad" => $nombre_unidad, 
			"nombreArea" => $nombre_area, 
			"areaSeleccionada" => $unidad->id_area_funcional, 
			"areasFuncionales" => $areas_funcionales, 
			"dotacion" => ($dotacion) ? $dotacion : 'Sin Numero',
			"subcategoria_unidad" => ($subCategoriaUnidad) ? $subCategoriaUnidad->id_subcategoria : null,
			"cama_temporal" => $unidad->cama_temporal
		]);
	}

	public function updateUnidad(Request $request){ log::info($request);
		try{
			DB::beginTransaction();
			
			$idUnidad=$request->input("idUnidad");
			$idEstab=$request->input("idEstab");
			$nombre=trim($request->input("nombre"));
			$tipo_unidad = $request->input("tipo-unidad");
			$area_funcional = $request->input("area-funcional");
			$alias=strtolower(Funciones::sanearString($nombre));
			$num_dotacion=trim($request->input("dotacion"));
			$subcategoria_unidad = $request->input("subcategoria_unidad");
			$cama_temporal = $request->input("cama_temporal");

			$unidad=UnidadEnEstablecimiento::where("id", "=", $idUnidad)->where("establecimiento", "=", $idEstab)->first();
			$unidad->alias=$nombre;
			$unidad->url=$alias;
			$unidad->tipo_unidad = $tipo_unidad;
			$unidad->id_area_funcional = $area_funcional;
			$unidad->cama_temporal = $cama_temporal;
			$unidad->save();

			$existe_historial_subcategoria = HistorialSubcategoriaUnidad::where('id_unidad',$idUnidad)->where('visible',true)->first();
			if($existe_historial_subcategoria){
				HistorialSubcategoriaUnidad::where("id",$existe_historial_subcategoria->id)->update([
					'fecha_modificacion' => Carbon::now()->format("Y-m-d H:i:s"),
					'usuario_modifica' => Auth::user()->id,
					'visible' => false
				]);

				$copia = new HistorialSubcategoriaUnidad;
				$copia->fecha = Carbon::now()->format('Y-m-d H:i:s');
				$copia->usuario_ingresa = Auth::user()->id;
				$copia->id_unidad = $existe_historial_subcategoria->id_unidad;
				$copia->id_subcategoria = ($subcategoria_unidad != '') ? $subcategoria_unidad : null;
				$copia->visible = true;
				$copia->save();
			}else{
				$nuevo = new HistorialSubcategoriaUnidad;
				$nuevo->fecha = Carbon::now()->format('Y-m-d H:i:s');
				$nuevo->usuario_ingresa = Auth::user()->id;
				$nuevo->id_unidad = $unidad->id;
				$nuevo->id_subcategoria = $subcategoria_unidad;
				$nuevo->visible = true;
				$nuevo->save();
			}

			//si encuentra una dotacion debe darla por terminada y marcar como false
			Dotacion::where("id_servicio", "=", $idUnidad)->where("visible", true)->update([
				'fecha_termino' => Carbon::now()->format("Y-m-d H:i:s"),
				'visible' => false
			]);

			$dotacion = new Dotacion;
			$dotacion->id_servicio = $idUnidad;
			$dotacion->visible = true;
			$dotacion->dotacion = $num_dotacion ? $num_dotacion : null;
			$dotacion->save();

			DB::commit();
			return response()->json(array("exito" => "La unidad ha sido editada exitosamente"));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al crear la unidad", "msg" => $ex->getMessage()));
		}
	}

	public function obtenerSalasCamas($idEstab, $idUnidad){
		$response=array();
		$_salas = Sala::where("establecimiento", $idUnidad)->where("visible","=",'TRUE')->get();
		foreach($_salas as $sala){
			/* @var $sala Sala*/
			$_camas = $sala->camas()->count();
			$url = URL::to('/');
			$opcion="<a href='$url/administracionUnidad/editarSala/$sala->id/$idEstab/$idUnidad'>Editar</a>";
			$response[]=array($sala->nombre, $_camas, $opcion);
		}
		return response()->json(["aaData" => $response]);
	}

	public function bloquearSala(Request $request){
		try{
			$idSala=$request->input("id");
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al crear la unidad.", "msg" => $ex->getMessage()));
		}
	}

	public function desbloquearSala(Request $request){
		try{
			$idSala=$request->input("id");
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al crear la unidad.", "msg" => $ex->getMessage()));
		}
	}

	public function crearSala(Request $request){
		try{
			$nombre=trim($request->input("sala"));
			$idEstab=$request->input("idUnidad");

			$sala=new Sala;
			$sala->nombre=$nombre;
			$sala->establecimiento=$idEstab;
			$sala->save();

			return response()->json(array("exito" => ""));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al crear la sala.", "msg" => $ex->getMessage()));
		}
	}

	public function updateNombreSala(Request $request){
		try{
			$nombre=trim($request->input("nombre"));
			$idSala=$request->input("idSala");

			$sala=Sala::find($idSala);
			$sala->nombre=$nombre;
			$sala->save();

			return response()->json(array("exito" => "El nombre de la sala cambiado exitosamente."));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al cambia el nombre de la sala.", "msg" => $ex->getMessage()));
		}
	}

	public function borrarSala(Request $request){
		try{
			DB::beginTransaction();
			$resultado = Sala::tieneCamasOcupadasOBloqueadas($request->idSala);
			DB::commit();
			if($resultado === 'vacio'){
				return response()->json(array("error" => "No existen camas para borrar"));
			}elseif($resultado){
				$idSala=$request->input("idSala");
				$sala=Sala::find($idSala);
				$sala->visible=FALSE;
				$sala->save();
	
				return response()->json(array("exito" => "Sala borrada exitosamente."));
			}else{
				return response()->json(array("error" => "Tiene camas ocupadas o bloqueadas"));
			}
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al borrar la sala.", "msg" => $ex->getMessage()));
		}
	}

	public function editarSalaView($idSala, $idEstab, $idUnidad){
		$sala=Sala::find($idSala);
		Log::info($sala);
		$nombreSala=ucwords($sala->nombre);
		$estab = Establecimiento::find($idEstab);
		$nombreEstab= $estab->nombre;
		$tipo_cama = TipoCama::seleccion();
		$tipo_unidad = TipoUnidad::seleccion();
		$alias=UnidadEnEstablecimiento::find($idUnidad)->alias;

		if($sala->visible == true){
			
			return View::make("AdministracionUnidad/EditarSala", ["estab" => $estab, "nombreSala" => $nombreSala, "idEstab" => $idEstab,
				"nombreEstab" => $nombreEstab, "idSala" => $idSala, "alias" => $alias, "idUnidad" => $idUnidad, "tipoCama" => $tipo_cama, "tipoUnidad" => $tipo_unidad]);
			}elseif($sala->visible == false){
			return View::make("AdministracionUnidad/SalaEliminada", ["estab" => $estab, "nombreSala" => $nombreSala, "idEstab" => $idEstab,
				"nombreEstab" => $nombreEstab, "idSala" => $idSala, "alias" => $alias, "idUnidad" => $idUnidad, "tipoCama" => $tipo_cama, "tipoUnidad" => $tipo_unidad]);

		}
	}

	public function agregarCamas(Request $request){
		try{
			$idSala=$request->input("idSala");
			$camas=$request->input("numCamas");
			$tipo = $request->input("tipo-cama");
			$tipo_unidad = $request->input("tipo-unidad");
			if($tipo == 0){
				$tipo = null;
			}
			$tipo_unidad = ($tipo_unidad == 0) ? null : $tipo_unidad;

			$num_camas = Cama::habilitadas()->with("tipoCama", "sala")->where("sala", $idSala)->count();
			$infocamas = Cama::select('id_cama')->habilitadas()->where("sala", $idSala)->get();
			$prueba = Cama::select('id_cama')->habilitadas()->where("sala", $idSala)->orderByDesc('id_cama')->get();

			$p = [];
			foreach ($prueba as $pr) {
				array_push($p, $pr->id_cama);
			}
			natsort($p);
			$p = array_pop($p);
			$partx=explode(" ",$p);
 			$idx = end($partx);
			$idn = intval($idx);

			$array = [];
			$array2 = [];
			foreach ($infocamas as $info) {
				array_push($array, $info->id_cama);
			}

			for ($i=0; $i < $idn; $i++) {
				$num = $i+1;
				array_push($array2, "CAMA {$num}");
			}

			$colleccion = collect($array);
			$colleccion = $colleccion->flatten();

			$colleccion2 = collect($array2);
			$colleccion2 = $colleccion2->flatten();

			$faltan2 = $colleccion2->diff($colleccion);
			$faltan2 = $faltan2->flatten();

			for($i=1; $i <= $camas; $i++){
				$cama=new Cama;
				$cama->sala=$idSala;
				$cama->tipo = $tipo;
				$cama->tipo_unidad = $tipo_unidad;
				$num_camas = $num_camas+1;
				$nombreCama = "CAMA {$num_camas}";
				$usado = $this->nombreUsado($nombreCama, $idSala);
				if($usado == 'si'){
					foreach ($faltan2 as $key => $f) {
						if($key >= 0){
							$cama->id_cama = $f;
						}
						break;
					}
				}else{
					$cama->id_cama = $nombreCama;
				}
				$cama->save();
			}

			$mensajeExito = ($camas > 1) ? 'Las camas han sido ingresadas exitosamente.' : 'La cama ha sido ingresada exitosamente';
			return response()->json(array("exito" => $mensajeExito));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al cambia el nombre de la sala.", "msg" => $ex->getMessage()));
		}
	}

	public function nombreUsado($nombreCama, $idSala){

		$nombresCamas = Cama::select('id_cama')->habilitadas()->where("sala", $idSala)->get();
		return ($nombresCamas->contains('id_cama', $nombreCama)) ? 'si' : 'no';
	}

	//este metodo renombrará las camas siempre en orden.
	public function renombrarCamas($idSala){
		$camas = Cama::select('id','id_cama')->habilitadas()->where("sala", $idSala)->orderBy('id', 'asc')->get();
		log::info("original: ".$camas);
		$cantidadCamas = $camas->count();
		log::info($cantidadCamas);
		foreach ($camas as $key => $cama) {
			log::info($key+1);
			$nombre = $key+1;
			$cama->id_cama = "CAMA {$nombre}";
			$cama->save();
		}
		log::info("editado: ".$camas);
	}

	public function obtenerCamasVigentes($idSala){
		/* @var $sala Sala*/
		$camas = Cama::habilitadas()->with("tipoCama", "tipoUnidad", "sala")->where("sala", $idSala)->get();
		$response = array();
		foreach($camas as $cama){
			$nombre=$cama->id_cama;
			if($cama->tipoCama){ $tipo = $cama->tipoCama->nombre;
			}else{ $tipo = null;}
			if($cama->tipoUnidad){
			$unidad = Cama::descripcionTipoUnidad($cama->tipo_unidad);
			}else{ $unidad = 'SIN ESPECIFICAR';}
			$fecha=(empty($cama->created_at)) ? "" : date("d-m-Y H:i", strtotime($cama->created_at));
			$bloquear="<a class='cursor' onclick='bloquearCama(\"$cama->id\", \"$cama->id_cama\")'>Bloquear</a>";
			$eliminar="<a class='cursor' onclick='eliminarCama(\"$cama->id\", \"$cama->id_cama\")'>Eliminar</a>";
			$cambiar="<a class='cursor' onclick='cambiarNombreCama(\"$cama->id\")'>Editar</a>";
			$opcion=$bloquear." - ".$eliminar." - ".$cambiar;
			$response[]=array($nombre, $tipo, $unidad, $fecha, $opcion);
		}
		return response()->json(["aaData" => $response]);
	}

	public function cambiarNombreCama(Request $request){
		try{
			$idCama=$request->input("idCama");
			$nombre=trim($request->input("nombre"));
			$tipo=$request->input("tipo-cama");
			$tipoUnidad=$request->input("tipo-unidad");
			if($tipo == 0){ $tipo = null;}
			if($tipoUnidad == 0){ $tipoUnidad = null;}

			$cama=Cama::find($idCama);
			$cama->id_cama=$nombre;
			$cama->tipo = $tipo;
			$cama->tipo_unidad = $tipoUnidad;
			$cama->save();

			return response()->json(array("exito" => ""));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al cambia el nombre de la cama.", "msg" => $ex->getMessage()));
		}
	}

	public function obtenerCamasEliminadas($idSala){
		$eliminadas = Cama::eliminadas()->where("sala", $idSala)->get();
		$response = array();
		foreach($eliminadas as $eliminada){
			$fecha = $fecha = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $eliminada->fecha)->format("d-m-Y H:i");
			$response[] = array($eliminada->id_cama, $fecha);
		}
		return response()->json(["aaData" => $response]);
	}

	public function obtenerCamasBloqueadas($idSala){
		$camas = Sala::find($idSala)->camas()->camasBloqueadas()->get();
		$response = array();
		/* @var $cama Cama */
		foreach($camas as $cama){
			$fecha = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $cama->bloqueos[0]->fecha)->format("d-m-Y H:i");
			$opcion = "<a class='cursor' onclick='desbloquearCama(\"{$cama->id}\",\"{$cama->id_cama}\")'>Desbloquear</a>";
			$response[] = array($cama->id_cama, $fecha, $cama->bloqueos[0]->motivo, $opcion);
		}
		return response()->json(["aaData" => $response]);
	}

	public function bloquearCama(Request $request){
		try{
			$idCama=$request->input("idCama");
			$motivo = $request->input("motivo");
			$cama = Cama::find($idCama);
			$cama->bloquear($motivo);
			return response()->json(array("exito" => ""));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al bloquear la cama.", "msg" => $ex->getMessage()));
		}
	}

	public function obtenerCama(Request $request){
		$cama = $request->input("idCama");
		return Cama::findOrFail($cama)->toJson();
	}

	public function desbloquearCama(Request $request){
		try{
			$idCama=$request->input("idCama");
			$usuario = Auth::user();
			Cama::find($idCama)->desbloquear("Desbloqueado por usuario {$usuario->nombres} {$usuario->apellido_paterno} {$usuario->apellido_materno}");
			return response()->json(array("exito" => ""));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al desbloquear la cama.", "msg" => $ex->getMessage()));
		}
	}

	public function cancelarCama(Request $request){
		try{
			$idCama=$request->input("idCama");
			return response()->json(array("exito" => ""));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al cancelar la cama.", "msg" => $ex->getMessage()));
		}
	}

	public function eliminarCama(Request $request){
		try{
			$idCama=$request->input("idCama");
			$borrar = new HistorialEliminacion;
			$borrar->cama = $idCama;
			$borrar->fecha = \Carbon\Carbon::now()->format("Y-m-d H:i:s");
			$borrar->save();
			return response()->json(array("exito" => ""));
		}catch(MensajeException $ex){
			return response()->json(array("error" => "Error al eliminar la cama: {$ex->getMessage()}"));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al eliminar la cama.", "msg" => $ex->getMessage()));
		}
	}

	public function obtenerServiciosOfrecidos($idEstab, $idUnidad){
		$response=array();
		$tieneServicios=UnidadEnEstablecimiento::getServiciosEstablecimientos($idEstab, $idUnidad);
		foreach($tieneServicios as $key => $value){
			$response[]=[$value];
		}
		return response()->json(["aaData" => $response]);

	}

	public function obtenerServiciosRecibidos($idEstab, $idUnidad){
		$response=array();
		$tieneServiciosRecibidos=UnidadEnEstablecimiento::getServiciosRecibidosEstablecimientos($idEstab, $idUnidad);
		foreach($tieneServiciosRecibidos as $key => $value){
			$response[]=[$value];
		}
		return response()->json(["aaData" => $response]);
	}

	public function editarServicioView($idEstab, $idUnidad, $servicio){
		$nombreServicio=($servicio == "ofrecido") ? "Servicios ofrecidos" : "Servicios recibidos";
		$estab = Establecimiento::find($idEstab);
		$nombreEstab=$estab->nombre;
		$alias=UnidadEnEstablecimiento::find($idUnidad)->alias;

		$param=["nombre" => $nombreEstab, "alias" => $alias,
			"idEstab" => $idEstab, "servicio" => $nombreServicio, "idUnidad" => $idUnidad, "estab" => $estab];

		if($servicio == "ofrecido") {

			$tiene_servicios = Unidad::whereHas("unidadesQueOfrecen", function($q) use ($idUnidad){
				$q->where("unidades_en_establecimientos.id", $idUnidad);
			})->get();
			$no_tiene_servicios = Unidad::whereDoesntHave("unidadesQueOfrecen", function($q) use ($idUnidad) {
				$q->where("unidades_en_establecimientos.id", $idUnidad);
			})->get();
			$tieneServicios = [];
			$noTieneServicios = [];
			foreach($tiene_servicios as $servicio){
				$tieneServicios[$servicio->id] = $servicio->nombre;
			}
			foreach($no_tiene_servicios as $servicio){
				$noTieneServicios[$servicio->id] = $servicio->nombre;
			}
			$param["tieneServicios"]=$tieneServicios;
			$param["noTieneServicios"]=$noTieneServicios;
			return View::make("AdministracionUnidad/EditarOfrecida", $param);
		}
		else{
			$tiene_servicios = Unidad::whereHas("unidadesQueReciben", function($q) use ($idUnidad){
				$q->where("unidades_en_establecimientos.id", $idUnidad);
			})->get();
			$no_tiene_servicios = Unidad::whereDoesntHave("unidadesQueReciben", function($q) use ($idUnidad) {
				$q->where("unidades_en_establecimientos.id", $idUnidad);
			})->get();
			$tieneServicios = [];
			$noTieneServicios = [];
			foreach($tiene_servicios as $servicio){
				$tieneServicios[$servicio->id] = $servicio->nombre;
			}
			foreach($no_tiene_servicios as $servicio){
				$noTieneServicios[$servicio->id] = $servicio->nombre;
			}
			$param["tieneServiciosRecibidos"]=$tieneServicios;
			$param["noTieneServiciosRecibidos"]=$noTieneServicios;
			return View::make("AdministracionUnidad/EditarRecibida", $param);
		}
	}

	public function getUnidades(Request $request){
		$estab=$request->input("establecimiento");
		$response=array();
		$unidades=Establecimiento::getUnidadPorEstablecimiento($estab);
		foreach ($unidades as $key => $value) {
			$response[]=array("nombre" => $value, "id" => $key);
		}
		return response()->json($response);
	}

	public function crearUnidad(Request $request){
		try{
			DB::beginTransaction();
			$nombre=trim($request->input("nombreUnidad"));
			$alias=strtolower(Funciones::sanearString($nombre));
			$estab=$request->input("establecimiento");
			$tipo_unidad = $request->input("tipo-unidad");
			$area_funcional = $request->input("area-funcional");
			$dotacion_cama = (int)trim($request->input("dotacionCamas"));
			$subCategoriaUnidad = $request->input("subcategoria_unidad");

			$unidadEn=new UnidadEnEstablecimiento;
			$unidadEn->establecimiento=$estab;
			$unidadEn->alias=$nombre;
			$unidadEn->url=$alias;
			$unidadEn->tipo_unidad = $tipo_unidad;
			$unidadEn->id_area_funcional = ($area_funcional) ? $area_funcional : 1;
			$unidadEn->save();

			$nuevaSubCategoriaUnidad = new HistorialSubcategoriaUnidad;
			$nuevaSubCategoriaUnidad->fecha = Carbon::now()->format('Y-m-d H:i:s');
			$nuevaSubCategoriaUnidad->usuario_ingresa = Auth::user()->id;
			$nuevaSubCategoriaUnidad->id_unidad = $unidadEn->id;
			$nuevaSubCategoriaUnidad->id_subcategoria = $subCategoriaUnidad;
			$nuevaSubCategoriaUnidad->visible = true;
			$nuevaSubCategoriaUnidad->save();

			$id_servicio = UnidadEnEstablecimiento::select("id")->where("alias", $nombre)->first();
			//log::info($id_servicio->id);
			$dotacion=new Dotacion;
			$dotacion->id_servicio=$id_servicio->id;
			$dotacion->visible=true;
			$dotacion->dotacion=$dotacion_cama ? $dotacion_cama : null;
			$dotacion->save();
			//log::info($dotacion);


			DB::commit();
			return response()->json(array("exito" => "La unidad ha sido creada exitosamente."));
		}catch(\Exception $ex){
			DB::rollback();
			log::info($ex);
			return response()->json(array("error" => "Error al crear la unidad, Intente con un nuevo nombre de unidad", "msg" => $ex->getMessage()));
		}
	}

	public function crearAreaFuncional(Request $request){
		// return $request;
		try {
			$nombre = trim(strip_tags($request->input("nombreArea")));
			DB::beginTransaction();

			$nueva_area_funcional = new AreaFuncional;
			$nueva_area_funcional->nombre = $nombre;
			$nueva_area_funcional->save();
			DB::commit();
			return response()->json(array("exito" => "El Área Funcional ha sido creada exitosamente."));
		} catch (\Exception $ex) {
			DB::rollback();
			return response()->json(array("error" => "Error al crear el Área Funcional", "msg" => $ex->getMessage()));
		}
	}

	public function todasAreasFuncionales(){
		return AreaFuncional::todasAreasFuncionales();
	}
	public function areasFuncionalesEstablecimientoOrdenadas(){
		return response()->json(AreaFuncional::areasFuncionalesEstablecimientoOrdenadas(session()->get("usuario")->establecimiento));
	}
	public function guardarOrdenAreasFuncionales(Request $request){
		try{
			$datos = $request->datos;
			$af = new AreaFuncional();
			foreach($datos as $dato){
				$af->guardarOrden($dato);
			}
			return response()->json(["error" => false]);
		} catch (\Exception $ex) {
			return response()->json(["error" => true,"msg" => $ex->getMessage()]);
		}
	}

	public function obtenerTodasAreasFuncionales(){
		$response = [];
		$areas_funcionales = AreaFuncional::where('id_area_funcional','<>', 1)
		->get(['id_area_funcional','nombre']);

		foreach ($areas_funcionales as $a) {
			$nombre = $a->nombre;
			$cambiar="<a class='cursor' onclick='cambiarNombreArea(\"$a->id_area_funcional\", \"$a->nombre\")'>Editar</a>";
			$opcion = $cambiar;
			$response[] = [$nombre, $opcion];
		}

		return response()->json(["aaData" => $response]);
	}

	public function updateAreaFuncional(Request $request){
		try {
			$nombre = $request->input("nombre-Area-Funcional");
			DB::beginTransaction();
			$area = AreaFuncional::find($request->idArea);
			$area->nombre = $nombre;
			$area->save();
			DB::commit();
			return response()->json(array("exito" => "El nombre del Área Funcional ha sido modificado exitosamente."));
		} catch (\Exception $ex) {
			log::info($ex);
			DB::rollback();
			return response()->json(array("error" => "Error al modificar el nombre del Área Funcional", "msg" => $ex->getMessage()));
		}

		return $request;
	}


	public function updateNombre(Request $request){
		try{
			DB::beginTransaction();
			$nombre=trim($request->input("nombreUnidad"));
			$alias=strtolower(Funciones::sanearString($nombre));
			$idUnidad=$request->input("unidad");

			$unidad=UnidadEnEstablecimiento::find($idUnidad);
			$unidad->alias=$nombre;
			$unidad->url=$alias;
			$unidad->save();

			DB::commit();
			return response()->json(array("exito" => "La nombre de la unidad ha sido actualizado."));
		}
		catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al actulizar el nombre de la unidad.", "msg" => $ex->getMessage()));
		}
	}

	public function updateServicios(Request $request){
		try{
			DB::beginTransaction();
			$unidadEn=$request->input("unidad");
			ServicioOfrecido::where("unidad_en_establecimiento", "=", $unidadEn)->delete();
			$servicios = $request->input("servicios");
			if(isset($servicios)) {
				foreach ($servicios as $servicio) {
					$ofrecido = new ServicioOfrecido;
					$ofrecido->unidad = $servicio;
					$ofrecido->unidad_en_establecimiento = $unidadEn;
					$ofrecido->save();
				}
			}
			DB::commit();
			return response()->json(array("exito" => "Los servicios han sidos actualizados."));
		}
		catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al actulizar los servicios.", "msg" => $ex->getMessage()));
		}
	}

	public function updateServiciosRecibidos(Request $request){
		try{
			DB::beginTransaction();
			$unidadEn=$request->input("unidad");
			ServicioRecibido::where("unidad_en_establecimiento", "=", $unidadEn)->delete();
			$servicios = $request->input("servicios");
			if(isset($servicios)) {
				foreach ($servicios as $servicio) {
					$ofrecido = new ServicioRecibido;
					$ofrecido->unidad = $servicio;
					$ofrecido->unidad_en_establecimiento = $unidadEn;
					$ofrecido->save();
				}
			}
			DB::commit();
			return response()->json(array("exito" => "Los servicios han sidos actualizados."));
		}
		catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al actulizar los servicios.", "msg" => $ex->getMessage()));
		}
	}

	public function getSalas(Request $request){
		$establecimiento=$request->input("establecimiento");
		$salas=Sala::getSalasEstablecimiento($establecimiento);
		return response()->json($salas);
	}

	public function crearCama(Request $request){
		try{
			$sala=$request->input("salasSelect");
			$nombre=trim($request->input("cama"));
			$diferenciacion=$request->input("diferenciacion");
			$tipoCama=$request->input("tipoCama");

			$cama=new Cama;
			$cama->id_cama=$nombre;
			$cama->diferenciacion=$diferenciacion;
			$cama->tipo=$tipoCama;
			$cama->sala=$sala;
			$cama->save();

			return response()->json(array("exito" => "La cama ha sido creada."));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al crear la cama.", "msg" => $ex->getMessage()));
		}
	}

	public function idSalaUnico(Request $request){
		$idSala=$request->input("id");
		$establecimiento=$request->input("establecimiento");
		$unico=Sala::idSalaUnico($idSala, $establecimiento);
		return response()->json(["unico" => $unico]);
	}

	public function getCamas(Request $request){
		$sala=$request->input("sala");
		$establecimiento=$request->input("establecimiento");
		$camas=Cama::getCamaPorSalaEstab($sala, $establecimiento);
		return response()->json($camas);
	}

	public function getSalasSelect(Request $request){
		$idEstablecimiento=$request->input("establecimiento");
		$salas=Sala::getSalasEstablecimiento($idEstablecimiento);
		return response()->json($salas);
	}

	public function editarSala(Request $request){
		try{
			$idSala=$request->input("idSalaH");
			$nombre=trim($request->input("sala"));

			$sala=Sala::find($idSala);
			$sala->nombre=$nombre;
			$sala->save();

			return response()->json(array("exito" => "La sala ha sido editada."));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al crear la sala.", "msg" => $ex->getMessage()));
		}
	}

	public function editarCama(Request $request){
		try{
			$idCama=$request->input("idCama");
			$nombre=trim($request->input("cama"));
			$diferenciacion=$request->input("diferenciacion");
			$tipoCama=$request->input("tipoCama");

			$cama=Cama::find($idCama);
			$cama->id_cama=$nombre;
			$cama->diferenciacion=$diferenciacion;
			$cama->tipo=$tipoCama;
			$cama->save();

			return response()->json(array("exito" => "La cama ha sido editada."));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al editar la cama.", "msg" => $ex->getMessage()));
		}
	}

}

?>
