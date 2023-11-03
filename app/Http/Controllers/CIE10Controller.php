<?php

namespace App\Http\Controllers;

use App\Models\CategoriaCIE10;
use Illuminate\Http\Request;
use DB;

class CIE10Controller extends Controller{

	public function consulta($palabra)
	{


		$datos=DB::select(DB::raw(
		"
		SELECT
		cc10.nombre AS nombre_categoria,
		c10.nombre AS nombre_cie10,
		cc10.id_categoria_cie_10 AS id_categoria,
		c10.id_cie_10 AS id_cie10
		FROM categoria_cie_10 AS cc10
		RIGHT JOIN cie_10 AS c10 ON cc10.id_categoria_cie_10=c10.id_categoria_cie_10
		WHERE  c10.visible=1
		AND c10.nombre ILIKE '%".$palabra."%'
		OR c10.id_cie_10 ILIKE '%".$palabra."%'
		ORDER BY cc10.id_categoria_cie_10 ASC,c10.id_cie_10 ASC
		"

		/* "
		SELECT
		c10.nombre AS nombre_cie10,
		c10.id_cie_10 AS id_cie10
		FROM cie_10 AS c10 
		WHERE  c10.visible=1
		AND c10.nombre ILIKE '%".$palabra."%'
		OR c10.id_cie_10 ILIKE '%".$palabra."%'
		ORDER BY c10.id_cie_10 ASC
		" */
	));




		return response()->json($datos);
	}


	public function consulta_categoria($palabra)
	{


		/* $datos=DB::select(DB::raw(
		"
		SELECT
		cc10.nombre AS nombre_categoria,
		cc10.id_categoria_cie_10 AS id_categoria,
		FROM categoria_cie_10 AS cc10
		INNER JOIN cie_10 AS c10 ON cc10.id_categoria_cie_10=c10.id_categoria_cie_10
		WHERE  c10.visible=1
		AND c10.nombre ILIKE '%".$palabra."%'
		ORDER BY cc10.id_categoria_cie_10 ASC
		LIMIT 10
		"

		
	)); */
		$datos = DB::table("categoria_cie_10")
		->select("nombre as nombre_categoria", "id_categoria_cie_10 as id_categoria")
		->where('nombre', 'like', '%'.$palabra.'%')
		//>limit(50)
		->get();
		return response()->json($datos);
	}
}

?>
