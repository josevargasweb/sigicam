<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Ambulancia;
use App\Models\Establecimiento;
use Carbon\Carbon;
use App\Models\Paciente;
use App\Models\EsperaAmbulancia;
use App\Models\Caso;
use DB;
use App\Models\Ruta;

class RutaController extends Controller
{
    //GENERA UNA RUTa
    public function ingresarRutas($ambulancia){

        $ambulancia_total = Ambulancia::where('id',$ambulancia)->first();

        $establecimientos = Establecimiento::all()
                      ->pluck('nombre','id');

        $pacientes = DB::table('espera_ambulancias')
                              ->select('pacientes.rut','pacientes.dv', 'espera_ambulancias.motivo', 'casos.establecimiento', 'pacientes.nombre', 'pacientes.apellido_paterno', 'pacientes.apellido_materno')
                              ->join('pacientes', 'pacientes.id', '=', 'espera_ambulancias.paciente_id')
                              ->join('casos', 'casos.paciente', '=', 'pacientes.id')
                              ->where('casos.fecha_termino',null)
                              ->groupBy('pacientes.rut','pacientes.dv', 'espera_ambulancias.motivo', 'casos.establecimiento', 'pacientes.nombre', 'pacientes.apellido_paterno', 'pacientes.apellido_materno')
                              ->get();

        $fecha_actual = Carbon::now();

        //RUTA ACTUAL

        $ruta_actual = DB::table('ambulancias')
                      ->select('rutas.hora_salida', 'rutas.hora_llegada_API', 'rutas.hospital_origen', 'rutas.hospital_destino')
                      ->join('rutas', 'rutas.ambulancia_id', 'ambulancias.id')
                      ->where('rutas.hora_salida','<', $fecha_actual)
                      ->where('rutas.hora_llegada_API','>', $fecha_actual)
                      ->orderBy('rutas.hora_salida', 'desc')
                      ->first();

        return view('Ambulancia.ingresarRutas')->with('ambulancia',$ambulancia_total)->with('establecimientos', $establecimientos)->with('pacientes',$pacientes)->with('ruta_actual', $ruta_actual);
    }

    //Ingresar Rutas

    public function listaRutas(Request $request){
      //dd($request);

      for ($i=0; $i < count($request->origen); $i++) { 
          $ruta[$i] = new Ruta();


          $ruta[$i]->hospital_origen = $request->origen[$i];
          
          $hora_salida = new Carbon($request->salida[$i]);
          $ruta[$i]->hora_salida = $hora_salida->toDateTimeString();

          $ruta[$i]->hospital_destino = $request->destino[$i];
          
          $hora_llegada = new Carbon($request->llegada[$i]);
          $ruta[$i]->hora_llegada_API = $hora_llegada->toDateTimeString();

          
          if($request->idPaciente[$i] != ""){
              $ruta[$i]->paciente_id = $request->idPaciente[$i];
          }else{
              $ruta[$i]->paciente_id = null;
          }
        
          $ruta[$i]->ambulancia_id = $request->id;
          $ruta[$i]->save();

          $estadoa_id = 1;
      }

      $ambulancia = DB::table('ambulancias')
                      ->leftjoin('rutas', 'rutas.ambulancia_id', '=', 'ambulancias.id')
                      ->where('ambulancias.id', $request->id)
                      ->orderBy('rutas.hora_salida','asc')
                      ->get();
      //dd($ambulancia);

      foreach ($ambulancia as $key => $value) {
        if ($value->hora_salida <= Carbon::now() && $value->estadoa_id != 3) {#la ambulancia ya salio del hospital 
          if ($value->hora_llegada_API >= Carbon::now()) {
            $estadoa_id = 4;#aun no ha llegado a su destino
          }elseif ($value->hora_llegada_API <= Carbon::now() && $estadoa_id != 4) {
            $estadoa_id = 2;#llego a su destino
          }
        }elseif ($value->hora_salida >= Carbon::now() && $estadoa_id != 4 && $value->estadoa_id != 3 ) {
          $estadoa_id = 1;
        }elseif ($value->estadoa_id == 3) {
          $estadoa_id = 3;
        }
      }

      $ambulancia_actualizar = Ambulancia::where('ambulancias.id', $request->id)
                      ->first();

      $ambulancia_actualizar->estadoa_id = $estadoa_id;
      $ambulancia_actualizar->save();

      $ambulancias = DB::table('ambulancias')
                      ->select('ambulancias.id', 'establecimientos.nombre', 'tipo_ambulancias.nombre', 'ambulancias.patente', 'ambulancias.enuso', 'ambulancias.capacidad', 'ambulancias.ubicacion')
                      ->join('establecimientos', 'establecimientos.id','=','ambulancias.establecimiento_id')
                      ->join('tipo_ambulancias', 'tipo_ambulancias.id','=','ambulancias.tipo_id')
                      ->join('estado_ambulancias', 'estado_ambulancias.id', '=', 'ambulancias.estadoa_id')
                      ->get();
      //dd($ambulancias);
      
      return redirect('ambulancia/indexRutas');
    }


    //editar rutas

    public function editarRutas($ambulancia){

      
    	$ambulancia = Ambulancia::where('id', $ambulancia)->first();

      $fecha_actual = Carbon::now();
    	
                    // dd($rutas); 
      $rutas = DB::table('rutas')
                    ->leftjoin('pacientes', 'pacientes.id', 'rutas.paciente_id')
                    ->where('rutas.ambulancia_id', $ambulancia->id)
                    ->where('rutas.hora_salida','>',$fecha_actual->toDateTimeString())  
                    ->get();

    	for ($i=0; $i < count($rutas); $i++) { 
    		//separar hora salida
    		$ano_mes_resto = explode('-', $rutas[$i]->hora_salida);
    		$dia_resto = explode(' ', $ano_mes_resto[2]);
    		$hora_minuto_segundo = explode(':', $dia_resto[1]);

    		$fecha = Carbon::create($ano_mes_resto[0],$ano_mes_resto[1],$dia_resto[0],$hora_minuto_segundo[0],$hora_minuto_segundo[1],$hora_minuto_segundo[2	]);

    		//dar formato a la fecha de salida
    		$fecha = $fecha->format('d-m-Y H:i:s');
    		$rutas[$i]->hora_salida =$fecha;

    		//separar fecha de llegada
    		$ano_mes_resto = explode('-', $rutas[$i]->hora_llegada_API);
    		$dia_resto = explode(' ', $ano_mes_resto[2]);
    		$hora_minuto_segundo = explode(':', $dia_resto[1]);

    		$fecha = Carbon::create($ano_mes_resto[0],$ano_mes_resto[1],$dia_resto[0],$hora_minuto_segundo[0],$hora_minuto_segundo[1],$hora_minuto_segundo[2	]);

    		//dar formato a la fecha de salida
    		$fecha = $fecha->format('d-m-Y H:i:s');
    		$rutas[$i]->hora_llegada_API =$fecha;


    		// dd($rutas[$i]);

    	}
    	    	
    	$establecimientos = Establecimiento::all()
                      ->pluck('nombre','id');

    	$carbon = Carbon::now();
    	$date = $carbon->now();

    	

      //RUTA ACTUAL

      $ruta_actual = DB::table('ambulancias')
                      ->select('rutas.hora_salida', 'rutas.hora_llegada_API', 'establecimientos.nombre as hospital_origen', 'rutas.hospital_destino')
                      ->join('rutas', 'rutas.ambulancia_id', 'ambulancias.id')
                      ->join('establecimientos', 'establecimientos.id', 'ambulancias.establecimiento_id')
                      ->leftjoin('pacientes', 'pacientes.id','rutas.paciente_id')
                      ->where('rutas.hora_salida','<', $fecha_actual)
                      ->where('rutas.hora_llegada_API','>', $fecha_actual)
                      ->orderBy('rutas.hora_salida', 'desc')
                      ->first();

      if ($ruta_actual != null) {
        $hospital_destino = DB::table('establecimientos')->where('id',$ruta_actual->hospital_destino)->first();
        $ruta_actual->hospital_destino = $hospital_destino->nombre;
      }
        // $rutas = Ruta::where('ambulancia_id', $ambulancia->id)
        //               ->where('rutas.hora_salida','>',$fecha_actual->toDateTimeString())  
        //               ->get();
      

      
      //dd($rutas);

    	return view('Ruta.editarRutas')->with('ambulancia',$ambulancia)->with('rutas', $rutas)->with('establecimientos', $establecimientos)->with('ruta_actual',$ruta_actual);
    }

    //guarda las rutas editadas
    public function listaRutas_guardar(Request $request){
    	// comprobar si la ruta no existe , de lo contrario eliminar rutas anteriores asociada ala ambulancia
      //return "holasasassa";
      //dd($request);
//esta wea esta fallamndo, arreglalo culiao :*
      $fecha_actual = Carbon::now();

    	$rutas = Ruta::select('rutas.id')
    				      ->join('ambulancias','ambulancias.id','=','rutas.ambulancia_id')
    	            ->where('ambulancias.id', $request->id)
                  ->where('rutas.hora_salida','>',$fecha_actual->toDateTimeString())
    	            ->get();

      // eliminar rutas actuales
      if(count($rutas) > 0){
          foreach ($rutas as $ruta) {
              $ruta_a_eliminar = Ruta::find($ruta->id);
              $ruta_a_eliminar->delete();

          }  
      }

      //ingresar nuevas rutas
    	for ($i=0; $i < count($request->origen); $i++) { 
    	    $ruta = new Ruta();
    	    $ruta->hospital_origen = $request->origen[$i];
    	    
    	    $hora_salida = new Carbon($request->salida[$i]);
    	    $ruta->hora_salida = $hora_salida->toDateTimeString();

    	    $ruta->hospital_destino = $request->destino[$i];
    	    
    	    $hora_llegada = new Carbon($request->llegada[$i]);
    	    $ruta->hora_llegada_API = $hora_llegada->toDateTimeString();
          //dd($request);
    	    
    	    if($request->idPaciente[$i] != ""){
              $ruta->paciente_id = $request->idPaciente[$i];
          }else{
              $ruta->paciente_id = null;
          }

          $ruta->ambulancia_id = $request->id;
          $ruta->save();

          $estadoa_id = 1;
    	}

      $ambulancia = DB::table('ambulancias')
                      ->leftjoin('rutas', 'rutas.ambulancia_id', '=', 'ambulancias.id')
                      ->where('ambulancias.id', $request->id)
                      ->orderBy('rutas.hora_salida','asc')
                      ->get();
      //dd($ambulancia);

      foreach ($ambulancia as $key => $value) {
        if ($value->hora_salida <= Carbon::now() && $value->estadoa_id != 3) {#la ambulancia ya salio del hospital 
          if ($value->hora_llegada_API >= Carbon::now()) {
            $estadoa_id = 4;#aun no ha llegado a su destino
          }elseif ($value->hora_llegada_API <= Carbon::now() && $estadoa_id != 4) {
            $estadoa_id = 2;#llego a su destino
          }
        }elseif ($value->hora_salida >= Carbon::now() && $estadoa_id != 4 && $value->estadoa_id != 3 ) {
          $estadoa_id = 1;
        }
      }

      //dd($estadoa_id);
      $ambulancia = Ambulancia::where('id', '=', $request->id)->first();
      $ambulancia->estadoa_id = $estadoa_id;
      $ambulancia->save();

    	return redirect('ambulancia/indexRutas');

      
    }

    //verifica si edita o agrega
    public function verificando($ambulancia){
        $fecha_actual = Carbon::now();
        $rutas = Ruta::select('rutas.id')
                  ->join('ambulancias','ambulancias.id','=','rutas.ambulancia_id')
                  ->where('ambulancias.id', $ambulancia)
                  ->where('rutas.hora_salida','>',$fecha_actual->toDateTimeString())
                  ->get();

        if(count($rutas) > 0){
            return redirect()->route('editarRutas', ['ambulancia' => $ambulancia]);
        }else{
            return redirect()->route('ingresarRutas', ['ambulancia' => $ambulancia]);
        }
    }

    public function historialRuta($ambulancia){

        $fecha_actual = Carbon::now();

        // busca rutas menores a la fecha actual
        $rutas = DB::table('rutas')
                  ->select('rutas.id', 'origen.nombre as origen','rutas.hospital_origen', 'rutas.hora_salida','rutas.hora_llegada_API','rutas.hospital_destino','pacientes.nombre','pacientes.rut','pacientes.dv', 'destino.nombre as destino')
                  ->join('ambulancias','ambulancias.id','=','rutas.ambulancia_id')
                  ->join('establecimientos as origen','origen.id','=','rutas.hospital_origen')
                  ->join('establecimientos as destino','destino.id','=','rutas.hospital_destino')
                  ->leftjoin('pacientes', 'pacientes.id', 'rutas.paciente_id')
                  ->where('ambulancias.id', $ambulancia)
                  ->where('rutas.hora_salida','<=',$fecha_actual->toDateTimeString())
                  ->get();
                  
        foreach ($rutas as $ruta) {
            $tSalida = Carbon::parse($ruta->hora_salida);
            $ruta->hora_salida = $tSalida->format('d-m-Y H:i:s');

            $tLlegada = Carbon::parse($ruta->hora_llegada_API);
            $ruta->hora_llegada_API = $tLlegada->format('d-m-Y H:i:s');
        }
        // dd($rutas);
        $ambulancia = Ambulancia::find($ambulancia);
        // dd($rutas);º

        return view('Ruta.historialRuta')->with('rutas',$rutas)->with('ambulancia',$ambulancia);
    }
}
