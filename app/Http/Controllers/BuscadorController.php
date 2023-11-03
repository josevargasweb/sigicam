<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use App\Models\Paciente;
use App\Models\Establecimiento;
use App\Models\ListaEspera;
use App\Models\HistorialDiagnostico;
use App\Models\Caso;
use App\Models\Sala;
use App\Models\AreaFuncional;
use App\Models\UnidadEnEstablecimiento;
use DB;
use Session;
use Log;
use Auth;
use HTML;
use Consultas;
use App\Models\THistorialOcupaciones;
use DateTime;
use App\Models\ListaTransito;

class BuscadorController extends Controller
{
    public function gestionBuscarAdm(){
        
        $salas=DB::table('salas')
                    ->join('unidades_en_establecimientos', 'salas.establecimiento', '=', 'unidades_en_establecimientos.id')
                    ->join('area_funcional', 'unidades_en_establecimientos.id_area_funcional', '=', 'area_funcional.id_area_funcional')
                    ->join('establecimientos', 'unidades_en_establecimientos.establecimiento', '=', 'establecimientos.id')
                    ->join('camas', 'salas.id', '=', 'camas.sala')
                    ->select('salas.*', 'salas.nombre as nombre_sala', 'unidades_en_establecimientos.*', 'area_funcional.nombre as area', 'establecimientos.nombre as nombre_estab', 'camas.id_cama as cama', 'unidades_en_establecimientos.id')
                    ->where('establecimientos.id', Auth::user()->establecimiento) 
                    ->get(); 
        

        return View::make("Administracion/Buscador", ["salas" => $salas]);
    }

    public function busqueda(Request $request)
    {
        $input = $request->all();

        if($request->get('busqueda')){
            $noticias = Sala::where("nombre", "LIKE", "%{$request->get('busqueda')}%")
                ->paginate(5);
        }else{
            $noticias = Sala::paginate(5);
        }

        //return response($noticias);
        return view("Administracion/buscadorbarra", compact("noticias"));
    }

}




        // $response=array();
        // foreach($establecimientos as $establecimiento){
        // $editar="<a class='cursor' onclick='editar(\"$establecimiento->nombre\", \"$establecimiento->rut\", $establecimiento->id)'>Editar</a>";
        // $link=HTML::link("/administracionUnidad/unidad/$establecimiento->id", "$establecimiento->nombre");
        //  $response[]=array("nombre" => $link, "rut" => ucwords($establecimiento->rut), "editar" => $editar);
        // }
        //return View::make("Administracion/Buscador", ["establecimientos" => $response, "rut" => Consultas::obtenerEnum("rut")]);
