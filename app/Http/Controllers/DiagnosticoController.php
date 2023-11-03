<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use PDF;
use Session;
use TipoUsuario;
use View;
use App\Models\HistorialDiagnostico;
use App\Models\Usuario;


class DiagnosticoController extends Controller{

	public function cargarDiagnosticos($caso){
        $idCaso = base64_decode($caso);
        $resultado = [];
		
        $diagnosticos = HistorialDiagnostico::select(
                "diagnosticos.diagnostico",
                "diagnosticos.id",
                "diagnosticos.id_cie_10",
                "diagnosticos.fecha","c.nombre",
                "diagnosticos.comentario")
            ->join("cie_10 as c","c.id_cie_10","diagnosticos.id_cie_10")
            ->where("caso", $idCaso)
            ->get();
        
        foreach ($diagnosticos as $diagn) {
            # code...
            $id_diagn= base64_encode($diagn->id);
            //$onclick = "onclick="modificarDiagnosticos(".$id_diagn");
            $resultado [] = [
                $diagn->fecha,
                "[".$diagn->id_cie_10."]".ucfirst($diagn->nombre)."  <div><strong>Comentario:</strong> <div id='$id_diagn'>".$diagn->comentario."</div></div>",
                "<button type='button' class='btn btn-xs btn-warning' onclick='modificarDiagnosticos($(this))' data-id='$id_diagn'>Editar</button>"
            ];
        }

        return response()->json(["aaData" => $resultado]);
	}

}

?>