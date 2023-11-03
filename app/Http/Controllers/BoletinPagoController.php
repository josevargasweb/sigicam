<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Boletin;
use App\Models\BoletinProducto;
use App\Models\THistorialOcupaciones;
use App\Http\UnidadController;

use Auth;
use DB;
use View;
use Log;
use PDF;
use Excel;

class BoletinPagoController extends Controller{

	public function __construct(){
		$this->idCaso=0;
		$this->fecha=0;
	}


	public static function guardarDatos($caso, $producto, $valor, $fecha, $cantidad){
        DB::beginTransaction();
        try{
            
            $boletin = Boletin::where("caso",$caso)->orderBy("id", "desc")->first();
            //comprobar si existe un boletin asociado al paciente
            if(!isset($boletin) ){
                $boletin = new Boletin;
                $boletin->caso = $caso;
                $boletin->save();
            }

            $fecha = Carbon::parse($fecha)->format("Y-m-d H:i:s");

            $boletinProducto = new BoletinProducto;
            $boletinProducto->id_boletin = $boletin->id;
            $boletinProducto->id_producto = $producto;
            $boletinProducto->id_usuario = Auth::user()->id;
            //$boletinProducto->valor = $valor;
            $boletinProducto->fecha = $fecha;
            $boletinProducto->cantidad = $cantidad;
            $boletinProducto->visible = true;
            $boletinProducto->save();

            DB::commit();

            return "exito";
        }catch(Exception $e){
            DB::rollback();
            return "error";
        }
	}
	
	public static function editarDatos($caso, $producto, $valor, $fecha, $cantidad, $idBP){
        DB::beginTransaction();
        try{
            //A peticion de camilo se tienen que conservar los boletines 
            $boletin = Boletin::where("caso",$caso)->orderBy("id", "desc")->first();

            $fecha = Carbon::parse($fecha)->format("Y-m-d H:i:s");

            if($idBP != null){
                //esto seria en caso de que presione el boton editar
                $boletinProductoCambiar = BoletinProducto::where("id", $idBP)->first();
                /* $boletinProducto->fecha = $fecha;
                $boletinProducto->id_producto = $producto; */
            }else{
                //en caso de que solo coincida con datos que el paciente tenia antes y es un aviso
                $boletinProductoCambiar = BoletinProducto::where("id_boletin", $boletin->id)
                        ->where("id_producto", $producto)
                        ->where("fecha", $fecha)
                        ->first();
            }
            
           /*  $boletinProducto->valor = $valor;
            $boletinProducto->cantidad = $cantidad; */
			$boletinProductoCambiar->visible = false;
            $boletinProductoCambiar->id_usuario_modifica = Auth::user()->id;
			$boletinProductoCambiar->tipo_modificacion = "Editar";
            $boletinProductoCambiar->save();

            $boletinProducto = new BoletinProducto;
            $boletinProducto->id_boletin = $boletin->id;
            $boletinProducto->id_producto = $producto;
            $boletinProducto->id_usuario = Auth::user()->id;
            //$boletinProducto->valor = $valor;
            $boletinProducto->fecha = $fecha;
            $boletinProducto->cantidad = $cantidad;
            $boletinProducto->visible = true;
			$boletinProducto->save();
			
			
            DB::commit();
            return "exito edicion";
        }catch(Exception $e){
            DB::rollback();
            return "error";
        }
    }

    public static function eliminarDato($idBP){
        DB::beginTransaction();
        try{
            $boletinProducto = BoletinProducto::where("id", $idBP)->first();
            $boletinProducto->visible = false;
            $boletinProducto->id_usuario_modifica = Auth::user()->id;
            $boletinProducto->tipo_modificacion = "Eliminar";
            $boletinProducto->save();
            
            //->delete();
            DB::commit();
            return "exito";
        }catch(Exception $e){
            DB::rollback();
            return "error";
        }
    }


	public function validarFechaBoletin(Request $request)
	{
		try{
			$fechaTmp = explode('-', $request->fecha);
			if (strlen($fechaTmp[2]) < 4 || strlen($fechaTmp[1]) < 2 || strlen($fechaTmp[0]) < 2 ) {
				//esto indica que el numero del año esta mal
				return response()->json(["valid" => false, "message" => "Ingrese un formato correcto (dd-mm-yyyy)"]);
			}

			$fecha = Carbon::parse($request->fecha)->endOfDay();			
			$idCaso = $request->caso;

			$validacion = THistorialOcupaciones::where("caso",$idCaso)
				->where("fecha_ingreso_real","<=",$fecha)
				->orderBy("fecha", "asc")->first();

			if($validacion){
				return response()->json(["valid" => true]);
			}else{
				return response()->json(["valid" => false, "message" => "El paciente tiene una fecha de ingreso posterior a la indicada"]);
			}


			
		} catch (Exception $e) {
			return response()->json(["valid" => false, "message" => "Error"]);
		}

		
	}
	public function obtenerListaProductosModificados(Request $request){
		$datos = DB::table("boletines as b")
					->select("bp.valor", "bp.cantidad", "bp.fecha","p.codigo","p.nombre","bp.id","u.nombres","u.apellido_paterno","u.apellido_materno", "bp.tipo_modificacion","bp.updated_at")
					->join("boletin_producto as bp", "bp.id_boletin","b.id")
					->join("productos as p","p.id","bp.id_producto")
					->leftjoin("usuarios as u","u.id","bp.id_usuario_modifica")
					->where("b.caso", $request->id)
					->where("bp.visible",false)
					->orderBy("bp.fecha","desc")
					->get();

		$resultado = [];

		foreach ($datos as $key => $dato) {
			$fecha = Carbon::parse($dato->fecha)->format("d-m-Y");
			$fecha_modificacion = Carbon::parse($dato->updated_at)->format("d-m-Y H:i:s");

			$resultado [] = [
				$fecha,
				$dato->nombre,
				$dato->codigo,
				$dato->cantidad,
				$dato->valor,
				$dato->nombres." ".$dato->apellido_paterno." ".$dato->apellido_materno,
				$dato->tipo_modificacion." (<b>".$fecha_modificacion."</b>)"
			];
		}

		return response()->json(["aaData" => $resultado]);
	}

    public function obtenerListaProductos(Request $request){

		$datos = DB::table("boletines as b")
					->select("p.valor", "bp.cantidad", "bp.fecha","p.codigo","p.nombre","bp.id","u.nombres","u.apellido_paterno","u.apellido_materno")
					->join("boletin_producto as bp", "bp.id_boletin","b.id")
					->join("productos as p","p.id","bp.id_producto")
					->leftjoin("usuarios as u","u.id","bp.id_usuario")
					->where("b.caso", $request->id)
					->where("bp.visible",true)
					->orderBy("bp.fecha","desc")
					->get();

		$resultado = [];

		foreach ($datos as $key => $dato) {
			$fecha = Carbon::parse($dato->fecha)->format("d-m-Y");

			$opciones = "<button class='btn btn-xs btn-danger' type='button' onclick='eliminarProducto(this,".$dato->id.")'>Borrar</button> <button class='btn btn-xs btn-warning' type='button' onclick='editarProducto(this,".$dato->id.")'>Editar</button>";

			$resultado [] = [
				$fecha,
				$dato->nombre,
				$dato->codigo,
				$dato->cantidad,
				$dato->valor*$dato->cantidad,
				$dato->nombres." ".$dato->apellido_paterno." ".$dato->apellido_materno,
				$opciones
			];
		}

		return response()->json(["aaData" => $resultado]);
    }

    public function consulta_productos($palabra)
	{
		$datos = DB::table("productos")
		->select(DB::raw("nombre, codigo, id, valor"))
		->where('visible',true)
		->where('nombre', 'like', '%'.strtoupper($palabra).'%')
        ->orWhere('codigo', 'like', '%'.strtoupper($palabra).'%')
		->orderBy('nombre', 'asc')
		->limit(50)
        ->get();
        
		return response()->json($datos);
	}

	public function ingresarProducto(Request $request){

		//valores
		$caso = $request->idCaso;
		$id_producto = $request->id_producto;
		$valor = $request->valor;
		$fecha = $request->fecha;
		$cantidad = $request->cantidad;
		$idBP = $request->id_boletin_producto;

		//Comprobar si ya tiene un codigo en ese dia
		$boletinEdicion = BoletinProducto::comprobarEdicion($caso, $id_producto, $fecha);

		if($boletinEdicion == "no hay datos"){
			//Guardar datos del boletin
			if($idBP != null){
				$boletinProducto = $this->editarDatos($caso, $id_producto, $valor, $fecha, $cantidad, $idBP);
			}else{
				$boletinProducto = $this->guardarDatos($caso, $id_producto, $valor, $fecha, $cantidad);
			}

			if($boletinProducto == "exito"){
				return response()->json(["exito" => "Producto ingresado correctamente"]);
			}elseif ($boletinProducto == "exito edicion") {
				return response()->json(["exito" => "Producto fue editado correctamente"]);
			}else{
				return response()->json(["error" => "Producto no pudo ser ingresado"]);
			}
		}else{
			//significa que encontro datos y es editable
			return response()->json(["editable" => $boletinEdicion]);
			
		}
		
		return response()->json($request);
	}
	
	public function editarProducto(Request $request){
		//valores
		$caso = $request->idCaso;
		$id_producto = $request->id_producto;
		$valor = $request->valor;
		$fecha = $request->fecha;
		$cantidad = $request->cantidad;
		$idBP = $request->id_boletin_producto;

		$boletinProducto = $this->editarDatos($caso, $id_producto, $valor, $fecha, $cantidad, $idBP);

		if($boletinProducto == "exito edicion"){
			return response()->json(["exito" => "Producto fue editado correctamente"]);
		}else{
			return response()->json(["error" => "Producto no pudo ser ingresado"]);
		}
	}


	public function eliminarProducto(Request $request){

		$this->eliminarDato($request->producto);
		return $request->producto;
	}

	public static function cargarProducto($idProducto){
		$producto = DB::table("boletin_producto as bp")
						->select("p.id as idProducto","p.nombre","p.codigo","p.valor","bp.fecha","bp.cantidad","bp.id as idBP")
						->join("productos as p","p.id", "bp.id_producto")
						->where("bp.id", $idProducto)
						->first();
		

		return response()->json([
			"idProducto" => $producto->idProducto,
			"nombre" => $producto->nombre,
			"codigo" => $producto->codigo,
			"valor" => $producto->valor*$producto->cantidad,
			"valorUnitario" => $producto->valor,
			"fecha" => Carbon::parse($producto->fecha)->format("d-m-Y"),
			"cantidad" => $producto->cantidad,
			"idBP" => $producto->idBP
		]);
	}

	public static function infoBoletinPagoHistorico($idCaso,$startMonth,$endMonth){
		//datos pacientes
		$paciente = DB::table("pacientes as p")
			->join("casos as c","c.paciente","p.id")
			->where("c.id", $idCaso)
			->first();

		$dv = ($paciente->dv == 10)?"K":$paciente->dv; 

		$paciente = [
			"nombre" => $paciente->nombre." ".$paciente->apellido_paterno." ".$paciente->apellido_materno,
			"rut" => $paciente->rut."-".$dv,
			"prevision" => $paciente->prevision,
		];

		$datos_cama = [
			"nombreCama" => "",
			"nombreSala" => "",
			"nombreUnidad" => "",
			"nombreEstablecimiento" => "",
			"fechaIngreso" => ""
		];

		//Calcular Unidad y Sala
		$datos_cama_tmp = DB::table("t_historial_ocupaciones as h")
				->select("c.id_cama as nombreCama","s.nombre as nombreSala", "u.alias as nombreUnidad","e.nombre as nombreEstablecimiento","h.fecha_ingreso_real")
				->join("camas as c","c.id","h.cama")
				->join("salas as s","s.id","c.sala")
				->join("unidades_en_establecimientos as u","u.id","s.establecimiento")
				->join("establecimientos as e","e.id","u.establecimiento")
				->where("h.caso",$idCaso)
				//->where("h.fecha",">=", $startMonth)
				->where("h.fecha","<=", $endMonth)
				->orderBy("h.id", "desc")
				->first();
		//Va a pescar la ultima ubiscacion del paciente

		if ($datos_cama_tmp) {
			$datos_cama = [
				"nombreCama" => $datos_cama_tmp->nombreCama,
				"nombreSala" => $datos_cama_tmp->nombreSala,
				"nombreUnidad" => $datos_cama_tmp->nombreUnidad,
				"nombreEstablecimiento" => $datos_cama_tmp->nombreEstablecimiento,
				"fechaIngreso" => Carbon::parse($datos_cama_tmp->fecha_ingreso_real)->format("d-m-Y H:i:s")
			];
		}

		//datos productos
		$productos = DB::table("boletin_producto as bp")
					->select("p.id as idProducto","p.nombre","p.codigo","p.valor","bp.fecha","bp.cantidad","bp.id as idBP","p.tipo")
					->join("productos as p","p.id", "bp.id_producto")
					->join("boletines as b", "b.id", "bp.id_boletin")
					->where("b.caso", $idCaso)
					->where("bp.fecha",">=", $startMonth)
					->where("bp.fecha","<=", $endMonth)
					->where("bp.visible","true")
					->orderBy("bp.fecha", "asc")
					->get();

		$lista_producto = [];
		$valor_total = 0;

		foreach ($productos as $key => $producto) {
			$valorTmp = $producto->valor*$producto->cantidad;

			//sueros
			$lista_producto []= [
				"nombre" => strtoupper($producto->nombre),
				"codigo" => $producto->codigo,
				"fecha" => Carbon::parse($producto->fecha)->format("d-m-Y"),
				"unidades" => $producto->cantidad,
				"valor" => $valorTmp
			];

			$valor_total += $valorTmp;
		}

		$array_info = [
			//informacion documento
			"fechaActual" => Carbon::now()->format("d-m-Y H:i:s"),
			"infoGeneral" => $datos_cama,
			//informacion paciente
			"paciente" => $paciente,
			//informacion medicamnetos, infumos y sueros
			"datos" => $lista_producto,
			"valor_total" => $valor_total,
		];

		return $array_info;
	}

	public static function infoBoletinPago($idCaso,$startMonth,$endMonth){

		//datos pacientes
		$paciente = DB::table("pacientes as p")
			->join("casos as c","c.paciente","p.id")
			->where("c.id", $idCaso)
			->first();

		$dv = ($paciente->dv == 10)?"K":$paciente->dv; 

		$paciente = [
			"nombre" => $paciente->nombre." ".$paciente->apellido_paterno." ".$paciente->apellido_materno,
			"rut" => $paciente->rut."-".$dv,
			"prevision" => $paciente->prevision,
		];

		$datos_cama = [
			"nombreCama" => "",
			"nombreSala" => "",
			"nombreUnidad" => "",
			"nombreEstablecimiento" => "",
			"fechaIngreso" => ""
		];

		//Calcular Unidad y Sala
		$datos_cama_tmp = DB::table("t_historial_ocupaciones as h")
					->select("c.id_cama as nombreCama","s.nombre as nombreSala", "u.alias as nombreUnidad","e.nombre as nombreEstablecimiento","h.fecha_ingreso_real")
					->join("camas as c","c.id","h.cama")
					->join("salas as s","s.id","c.sala")
					->join("unidades_en_establecimientos as u","u.id","s.establecimiento")
					->join("establecimientos as e","e.id","u.establecimiento")
					->where("h.caso",$idCaso)
					//->where("h.fecha",">=", $startMonth)
					->where("h.fecha","<=", $endMonth)
					->orderBy("h.id", "desc")
					->first();
		//Va a pescar la ultima ubiscacion del paciente
		
		if ($datos_cama_tmp) {
			$datos_cama = [
				"nombreCama" => $datos_cama_tmp->nombreCama,
				"nombreSala" => $datos_cama_tmp->nombreSala,
				"nombreUnidad" => $datos_cama_tmp->nombreUnidad,
				"nombreEstablecimiento" => $datos_cama_tmp->nombreEstablecimiento,
				"fechaIngreso" => Carbon::parse($datos_cama_tmp->fecha_ingreso_real)->format("d-m-Y H:i:s")
			];
		}
		
		//datos productos
		$productos = DB::table("boletin_producto as bp")
						->select("p.id as idProducto","p.nombre","p.codigo","p.valor","bp.fecha","bp.cantidad","bp.id as idBP","p.tipo")
						->join("productos as p","p.id", "bp.id_producto")
						->join("boletines as b", "b.id", "bp.id_boletin")
						->where("b.caso", $idCaso)
						->where("bp.fecha",">=", $startMonth)
						->where("bp.fecha","<=", $endMonth)
						->where("bp.visible","true")
						->orderBy("bp.fecha", "asc")
						->get();


		$lista_producto = [];
		$lista_fechas = [];
		$valor_total = [];

		//guarda producto con cantidad y fecha. 
		//array[id_producto] = (nombre_producto, array(fecha [dia.mes],cantidad),..., array(fecha [dia.mes],cantidad) ) y 
		//array valor_total [id_producto] = suma total de valores

		foreach ($productos as $key => $producto) {
			$fecha= Carbon::parse($producto->fecha)->format("d");
			$valorTmp = $producto->valor*$producto->cantidad;

			if(!array_key_exists($producto->idProducto,$lista_producto)){
				$lista_producto[$producto->idProducto] []= [
					$producto->nombre,
					$producto->codigo
				];
				$valor_total[$producto->idProducto] = $valorTmp;
			}else{
				$valor_total[$producto->idProducto] += $valorTmp;
			}
			$lista_producto[$producto->idProducto] []= [
				"fecha" =>$fecha,
				"cantidad" => $producto->cantidad,
				"valor" => $valorTmp
			];
			
			if(!in_array($fecha,$lista_fechas)){
				$lista_fechas [] = $fecha;
			}
		}

		$datos = [];
		$total = 0;
		 
		foreach ($lista_producto as $key => $p) {
			//cantidad y valor iniciado por cada producto
			$cantidad = 0;
			$valor = 0;

			//primeras 2 casillas seran el nombre y su codigo
			$datos [$key] = [
				$p[0][0],
				$p[0][1]
			]; 

			//recorre las fechas
			foreach ($lista_fechas as  $f) {

				//recorre dato por dato buscando si encuentra o no 
				$encontrado = 0;
				foreach ($p as $key_p => $p_tmp) {
					if ($encontrado == 0) {
						if ($key_p != 0) {
							if($p_tmp["fecha"] == $f) {
								//añade el valor a la matriz
								array_push($datos[$key], $p_tmp["cantidad"]);
								//suma cantidad de implementos usados
								$cantidad += $p_tmp["cantidad"];
								$valor += $p_tmp["valor"];
								$encontrado++;
							}
						}
					}else{
						//ya enocntro la fecha
						break;
					}
					
				}
				// si no encontro se pondra el valor 0 para indicar que en esa fecha no se encontro datos del producto
				if ($encontrado == 0) {
					array_push($datos[$key], 0);
				}
			}
			//ingresar la cantidad total y el valor total del producto gastado
			array_push($datos[$key], $cantidad);
			array_push($datos[$key], $valor);
			$total += $valor;
		}

		$array_info = [
			//informacion documento
			"fechaActual" => Carbon::now()->format("d-m-Y H:i:s"),
			"infoGeneral" => $datos_cama,
			//informacion paciente
			"paciente" => $paciente,
			//informacion productos
			"fechas" => $lista_fechas,
			"datos" => $datos,
			"total" => $total

		];
		return $array_info;

	}

	public static function exportarBoletinHistoricoPDF($idCaso){
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		
		//return $fecha_documento;
		$fecha_documento = [
			$meses[(int)Carbon::now()->format("m")-1],
			Carbon::now()->format("Y"),
		];

		$pac = THistorialOcupaciones::where("caso",$idCaso)->orderby("id","asc")->first();

		//fecha y mes solicitado
		$startMonth = Carbon::parse($pac->fecha)->startOfMonth();
		$endMonth   = Carbon::now()->format("Y-m-d 01:00:00");

		
		
		$info = self::infoBoletinPagoHistorico($idCaso,$startMonth,$endMonth);
		$info["fecha"] = $fecha_documento;

		$snappyPdf = PDF::loadView('Gestion.boletinPago.pdfBoletinPagoHistorico',$info);
		return $snappyPdf->stream('Boletin_de_Pago_Historico.pdf');
	}

	public static function exportarBoletinHistoricoExcel($idCaso){

		Excel::create('Boletin_de_Pago_Historico', function($excel) use ($idCaso) {
			$excel->sheet('Boletin_de_Pago_Historico', function($sheet) use ($idCaso) {
				
				$sheet->mergeCells('A1:P1');
				$sheet->setAutoSize(true);
				//$sheet->setWidth('A', 5);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {

					// call cell manipulation methods
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");
				
				});

				/* $fecha = $anno."-".$mes."-1"; */
				$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

				$fecha_documento = [
					$meses[(int)Carbon::now()->format("m")-1],
					Carbon::now()->format("Y"),
				];
		
				$pac = THistorialOcupaciones::where("caso",$idCaso)->orderby("id","asc")->first();
		
				//fecha y mes solicitado
				$startMonth = Carbon::parse($pac->fecha)->startOfMonth();
				$endMonth   = Carbon::now()->format("Y-m-d 01:00:00");
		
				
				
				$info = self::infoBoletinPagoHistorico($idCaso,$startMonth,$endMonth);
				$info["fecha"] = $fecha_documento;

				$sheet->loadView('Gestion.boletinPago.excelBoletinPagoHistorico', [
					"info" => $info,
				]);


			});
		})->download('xls');
	}

	public static function exportarBoletinPDF($idCaso,$fecha){
		
		/* $fecha = $anno."-".$mes."-1"; */
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$startMonth = Carbon::parse($fecha)->startOfMonth();
		$endMonth   = Carbon::parse($fecha)->format("Y-m-d 01:00:00");

		//fecha y mes solicitado
		$fecha_documento = [
			$meses[(int)Carbon::parse($fecha)->format("m")-1],
			Carbon::parse($fecha)->format("Y"),
		];
				
		$hospital = "";//hospital del paciente
		$unidad = "";
		
		$info = self::infoBoletinPago($idCaso,$startMonth,$endMonth);
		$info["fecha"] = $fecha_documento;
		
		//return response()->json($info);
		$snappyPdf = PDF::loadView('Gestion.boletinPago.pdfBoletinPago',$info);
		return $snappyPdf->stream('Boletin_de_Pago_'.$fecha.'.pdf');
		
	}

	public static function exportarBoletinExcel($idCaso,$fecha){
		
		
		
		/* $snappyPdf = PDF::loadView('Gestion.boletinPago.pdfBoletinPago',$info);
		return $snappyPdf->stream('Boletin_de_Pago_'.$fecha.'.pdf'); */

		Excel::create('Boletin_de_Pago_'.$fecha, function($excel) use ($idCaso, $fecha) {
			$excel->sheet('Boletin_de_Pago_'.$fecha, function($sheet) use ($idCaso, $fecha) {
				
				$sheet->mergeCells('A1:P1');
				$sheet->setAutoSize(true);
				//$sheet->setWidth('A', 5);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {

					// call cell manipulation methods
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");
				
				});

				/* $fecha = $anno."-".$mes."-1"; */
				$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
				$startMonth = Carbon::parse($fecha)->startOfMonth();
				$endMonth   = Carbon::parse($fecha)->format("Y-m-d 01:00:00");

				//fecha y mes solicitado
				$fecha_documento = [
					$meses[(int)Carbon::parse($fecha)->format("m")-1],
					Carbon::parse($fecha)->format("Y"),
				];
						
				$hospital = "";//hospital del paciente
				$unidad = "";
				
				$info = self::infoBoletinPago($idCaso,$startMonth,$endMonth);
				$info["fecha"] = $fecha_documento;

				$sheet->loadView('Gestion.boletinPago.excelBoletinPago', [
					"info" => $info,
				]);


			});
		})->download('xls');
		
	}
	

	
	
	
}
