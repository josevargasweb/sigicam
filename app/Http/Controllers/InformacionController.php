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

class InformacionController extends Controller
{
   	public function indexRutas(){
   		$ambulancias = Ambulancia::select('ambulancias.id', 'ambulancias.patente','tipo_ambulancias.nombre as tipo', 'ambulancias.enuso','ambulancias.capacidad', 'establecimientos.nombre as nestablecimiento', 'ambulancias.ubicacion','ambulancias.estadoa_id')
                    ->join('tipo_ambulancias','ambulancias.tipo_id','=','tipo_ambulancias.id')
                    ->join('establecimientos','ambulancias.establecimiento_id','=','establecimientos.id')
                    ->get();


		  return view('Ambulancia.indexRutas')->with('ambulancias',$ambulancias);
   	}

   	public function enviarAmbulancia(Request $request){
   		$ambulancia = Ambulancia::where('patente',$request->patente)->first();
      $ubicacion =Establecimiento::where('nombre',$ambulancia->ubicacion)->first();




   		$establecimientos = Establecimiento::where('id','<>' ,$ubicacion->id)
   									->pluck('nombre','id');

      $pacientes = DB::table('pacientes')
                            ->select('pacientes.rut','pacientes.dv', 'espera_ambulancias.motivo', 'casos.establecimiento', 'pacientes.nombre', 'pacientes.apellido_paterno', 'pacientes.apellido_materno')
                            ->join('espera_ambulancias', 'espera_ambulancias.paciente_id', '=', 'pacientes.id')
                            ->join('casos', 'casos.paciente', '=', 'pacientes.id')
                            ->where('casos.establecimiento', $ubicacion->id)
                            ->groupBy('pacientes.rut','pacientes.dv', 'espera_ambulancias.motivo', 'casos.establecimiento', 'pacientes.nombre', 'pacientes.apellido_paterno', 'pacientes.apellido_materno')
                            ->get();
                            // dd($pacientes);

                            // dd($ubicacion->nombre);

   		return view('Ambulancia.enviarAmbulancia')->with('ambulancia',$ambulancia)->with('establecimientos', $establecimientos)->with('pacientes',$pacientes);
   	}

   	public function generarhora(Request $request){

   		$fecha = Carbon::create($request->ano, $request->mes, $request->dia, $request->hora, $request->minuto, $request->segundo);

   		$hospital_origen = Ambulancia::select('establecimiento_id')
   									->where('patente',$request->patente)
   									->first();
   		//distancia
        //ESTABLECIMIENTO ORIGEN
        $establecimiento_origen = Establecimiento::where('id', $hospital_origen->establecimiento_id)->first();
        $lat_origen = $establecimiento_origen->latitud;
        $long_origen = $establecimiento_origen->longitud;

        //ESTABLECIMIENTO DESTINO
        $establecimiento_destino = Establecimiento::where('id', $request->destino)->first();
        $lat_destino = $establecimiento_destino->latitud;
        $long_destino = $establecimiento_destino->longitud;

        $x = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$lat_origen.','.$long_origen.'&destinations='.$lat_destino.','.$long_destino.'&key=AIzaSyAeEOKLHeC8EzWrPJpqyBDJYAMbZRfV09o'));

        $minutos = explode(" ", $x->rows[0]->elements[0]->duration->text);


        $fecha->addMinutes($minutos[0]);
        $fecha->toDateTimeString();
        $fecha = $fecha->format('d-m-Y H:i:s');
        // dd($fecha);

   		return response()->json(array('fecha' => $fecha));
   	}

   	public function ingresarEsperaAmbulancia(Request $request){

   		$paciente = Paciente::where('id', $request->paciente)->first();
      $establecimiento = Caso::select('establecimiento')
                            ->where('id', $request->caso)
                            ->first();
                            // dd($establecimiento);
      $existe = EsperaAmbulancia::where('paciente_id',$request->paciente)
                            ->where('estado','activo')
                            ->first();

      if($existe == null){
          $espera_ambulancia = new EsperaAmbulancia();
          $espera_ambulancia->hora_ambulancia_requerida = $request->salida;
          $espera_ambulancia->motivo = $request->motivo;
          $espera_ambulancia->estado = 'activo';
          $espera_ambulancia->paciente_id = $request->paciente;
          $espera_ambulancia->establecimiento_id = $establecimiento->establecimiento;
          // dd($espera_ambulancia);
          $espera_ambulancia->save();
      }else{
          $existe->hora_ambulancia_requerida = $request->salida;
          $existe->motivo = $request->motivo;
          $existe->estado = 'activo';
          $existe->paciente_id = $request->paciente;
          $existe->establecimiento_id = $establecimiento->establecimiento;
          // dd($espera_ambulancia);
          $existe->save();
      }

   		return redirect('unidad/'.$request->unidad);

   	}


    // GENERA VARIAS RUTAS
    public function generarHoraRuta(Request $request){

        // dd($request);
        $fecha = Carbon::create($request->ano, $request->mes, $request->dia, $request->hora, $request->minuto, $request->segundo);

        //ESTABLECIMIENTO ORIGEN
        $establecimiento_origen = Establecimiento::where('id', $request->origen)->first();
        $lat_origen = $establecimiento_origen->latitud;
        $long_origen = $establecimiento_origen->longitud;

        //ESTABLECIMIENTO DESTINO
        $establecimiento_destino = Establecimiento::where('id', $request->destino)->first();
        $lat_destino = $establecimiento_destino->latitud;
        $long_destino = $establecimiento_destino->longitud;

        $x = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$lat_origen.','.$long_origen.'&destinations='.$lat_destino.','.$long_destino.'&key=AIzaSyAeEOKLHeC8EzWrPJpqyBDJYAMbZRfV09o'));

        $minutos = explode(" ", $x->rows[0]->elements[0]->duration->text);
        if(count($minutos) == 2){
            $fecha->addMinutes($minutos[0]);
            $fecha->toDateTimeString();
            $fecha = $fecha->format('d-m-Y H:i:s');
        }else if(count($minutos) == 4){
            $fecha->addHours($minutos[0]);
            $fecha->addMinutes($minutos[2]);
            $fecha->toDateTimeString();
            $fecha = $fecha->format('d-m-Y H:i:s');
        }



        // dd(count($minutos));
      return response()->json(array('fecha' => $fecha));
    }

    // Busca pacientes de un determinado hospital
    public function buscarPacientes(Request $request){

        $pacientes = DB::table('espera_ambulancias')
                              ->select('pacientes.rut','pacientes.dv', 'espera_ambulancias.motivo', 'casos.establecimiento', 'pacientes.nombre', 'pacientes.apellido_paterno', 'pacientes.apellido_materno', 'espera_ambulancias.paciente_id')
                              ->leftjoin('pacientes', 'pacientes.id', '=', 'espera_ambulancias.paciente_id')
                              ->leftjoin('casos', 'casos.paciente', '=', 'pacientes.id')
                              ->where('espera_ambulancias.establecimiento_id',$request->establecimiento_id)
                              ->where('casos.fecha_termino',null)
                              ->get();

        $response = array();
        foreach ($pacientes as $paciente) {
            if ($paciente->rut != null) {
              $boton = "<button class= 'btn btn-warning' type='button' onclick= cargarPacientes($paciente->paciente_id,false,$paciente->rut-$paciente->dv,$request->indice) data-rut='$paciente->rut-$paciente->dv'>Agregar</button>";
            }else{
              $boton = "<button class= 'btn btn-warning botonql' type='button' data-indiceorigen='$request->indice' data-rut='$paciente->rut-$paciente->dv' data-nombre='$paciente->nombre' data-idPaciente=$paciente->paciente_id>Agregar</button>";
            }
            
            $response[] = array($paciente->nombre, $paciente->rut.'-'.$paciente->dv, $paciente->motivo, $boton );
        }
        
        return response()->json($response);
    }



}
