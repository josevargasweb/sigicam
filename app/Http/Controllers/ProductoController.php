<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Producto;
use App\Models\Establecimiento;

use Auth;
use DB;
use View;
use Log;

class ProductoController extends Controller
{
    
    public function indexProductos(){
        
		$productosHabilitados = Producto::infoProductoHabilitados();
		$productosDeshabilitados = Producto::infoProductoDeshabilitados();
		
			return view::make("Productos/IndexProductos", [
				"productos" => $productosHabilitados,
				"deshabilitados" => $productosDeshabilitados
			]);
			//return 'oka';
    }
    

    public function deshabilitarProducto(Request $request){
        DB::beginTransaction();
        try {
            $id_producto = $request->input("id_producto");
            $producto = Producto::find($id_producto);
            $producto->visible = false;
            $producto->save();
            DB::commit();
            return response()->json(["exito" => "El producto ha sido deshabilitado"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al deshabilitar al producto", "msg" => $ex->getMessage()]);
        }
    }

    public function habilitarProducto(Request $request){
        DB::beginTransaction();
        try {
            $id_producto = $request->input("id_producto");
            $producto = Producto::find($id_producto);
            $producto->visible = true;
            $producto->save();
            DB::commit();
            return response()->json(["exito" => "El producto ha sido habilitado"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al habilitar el producto", "msg" => $ex->getMessage()]);
        }
    }

    public function editarProducto($id_producto){
        $producto = Producto::find($id_producto);

        return view::make("Productos/EditarProducto", [
            "producto" => $producto
        ]);
    }

    public function actualizarDatosProducto(Request $request){
        DB::beginTransaction();
        try {
			$producto = Producto::find($request->id_producto);
			$producto->codigo = $request->codigo_producto;
            $producto->nombre = strtoupper($request->nombre_producto);     
            /* $producto->tipo = $request->tipo_producto; */
            $producto->valor = $request->valor_producto;
            $producto->visible = ($request->visible_producto)?true:false;
            $producto->id_usuario_modifica = Auth::user()->id;
            $producto->tipo_modificacion = "Editar producto";
            $producto->save();

            DB::commit();
			return response()->json(["exito" => "El producto ha sido modificado"]);
		} catch (Exception $ex) {
            DB::rollback();
			return response()->json(["error" => "Error al modificar el producto", "msg" => $ex->getMessage()]);
		}
    }

    public function actualizarValorProducto(Request $request){
        DB::beginTransaction();
        try {
            $producto = Producto::find($request->id_producto);
            $producto->visible = false;
            $producto->id_usuario_modifica = Auth::user()->id;
            $producto->tipo_modificacion = "Actualizar valor";
            $producto->save();

            $pro_new = new Producto;
			$pro_new->codigo = $producto->codigo;
            $pro_new->nombre = strtoupper($producto->nombre);     
            /* $pro_new->tipo = $producto->tipo; */
            $pro_new->valor = $request->valor_producto;
            $pro_new->id_establecimiento = $producto->id_establecimiento;
            $pro_new->visible = true;
            $pro_new->save();

            DB::commit();
			return response()->json(["exito" => "El producto ha sido actualizado"]);
		} catch (Exception $ex) {
            DB::rollback();
			return response()->json(["error" => "Error al actualizado el producto", "msg" => $ex->getMessage()]);
		}
    }

    public function actualizarProducto($id_producto){
        $producto = Producto::find($id_producto);

        return view::make("Productos/ActualizarProducto", [
            "producto" => $producto
        ]);
    }


    public function registrarProducto(Request $request){
        DB::beginTransaction();
        try {            
            $producto = new Producto;
            $producto->codigo = $request->codigo_producto;
            $producto->nombre = strtoupper($request->nombre_producto);            
            $producto->id_establecimiento = Auth::user()->establecimiento;
           /*  $producto->tipo = $request->tipo_producto; */
            $producto->valor = $request->valor_producto;
            $producto->visible = ($request->visible_producto)?true:false;
            $producto->save();

            DB::commit();
            return response()->json(["exito" =>"El producto ha sido creado exitosamente"]);
		} catch (Exception $ex) {
            DB::rollback();
			return response()->json(["error" => "Error al crear el producto"]);
		}

    }    

}
