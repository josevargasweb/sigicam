<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Establecimiento;


use App\Models\Ambulancia;
use App\Models\TipoAmbulancia;
use App\Models\EstadoAmbulancia;
use App\Models\Ruta;


use App\Models\PermisosEstablecimiento;
use Laracasts\Flash\Flash;
use DB;
use Carbon\Carbon;
use App\Models\Usuario;



class AmbulanciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ambulancias = Ambulancia::select('ambulancias.id', 'ambulancias.patente','tipo_ambulancias.nombre as tipo', 'ambulancias.enuso','ambulancias.capacidad', 'establecimientos.nombre as nestablecimiento', 'ambulancias.ubicacion','ambulancias.estadoa_id')
                    ->where('estadoa_id','<>',7)
                    ->join('tipo_ambulancias','ambulancias.tipo_id','=','tipo_ambulancias.id')
                    ->join('establecimientos','ambulancias.establecimiento_id','=','establecimientos.id')
                    ->get();
        
    
        //dd($ubicacion->nombre);
        $establecimientos = Establecimiento::all()->pluck('nombre','id');
        $tipoAmbulancias = TipoAmbulancia::all()->pluck('nombre','id');
        $estados = EstadoAmbulancia::all()->pluck('estado', 'id');
        

        return view('Ambulancia.index')->with('ambulancias',$ambulancias)->with('establecimientos',$establecimientos)->with('tipos',$tipoAmbulancias)->with('estados',$estados);
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //En el mismo index se abre un modal, por loqeu esta funcionalidad queda inutilizable
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $establecimiento = Establecimiento::where('id',$request->establecimiento_id)->first();
        $ubicacion = Establecimiento::where('id', $request->ubicacion)->first();
        $ambulancia = new Ambulancia($request->all());
        $ambulancia->enuso = 0;
        $ambulancia->capacidad = 1;
        $ambulancia->ubicacion = $ubicacion->nombre;
        $ambulancia->estadoa_id = 2;
        $ambulancia->save();

        // Flash::success("Se ha registrado la ambulancia con patente ".$ambulancia->patente." de forma exitosa!!");

        return redirect()->route('ambulancias.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ambulancia = Ambulancia::find($id);
        $estados = EstadoAmbulancia::whereIn('id', [1,2,3])
                        ->pluck('estado','id');

        $tipoAmbulancias = TipoAmbulancia::all()->pluck('nombre','id');
        
        $establecimientos = Establecimiento::all()->pluck('nombre','id');
        
        return view('Ambulancia.edit')->with('ambulancia',$ambulancia)->with('establecimientos', $establecimientos)->with('tipos', $tipoAmbulancias)->with('estados', $estados);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ambulancia = Ambulancia::find($id);

        $ambulancia->patente = $request->patente;
        $ambulancia->tipo_id = $request->tipo_id;
        $ambulancia->estadoa_id = $request->estadoa_id;
        $ambulancia->establecimiento_id = $request->establecimiento_id;
        
        $ubicacion = Establecimiento::where('id',$request->ubicacion)->first();
        $ambulancia->ubicacion = $ubicacion->nombre;

        $ambulancia->save();
        //dd($ambulancia);
        //Flash::warning("Ambulancia: ". $ambulancia->patente." del Establecimiento: ". $ambulancia->establecimiento->nombre." ha sido Editado de forma Satisfactoria");

        return redirect()->route('ambulancias.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ambulancia = Ambulancia::find($id);
        $rutas = Ruta::where('ambulancia_id',$ambulancia->id)->get();

        if(count($rutas) == 0){
            $ambulancia->delete();
        }else{
            //cambiar a estado eliminado
            $ambulancia->estadoa_id = 7;
            $ambulancia->save();
        }


        //Flash::warning("Ambulancia ". $ambulancia->patente." del Hospital: ". $ambulancia->establecimiento->nombre." Eliminado de forma Correcta");
        return redirect()->route('ambulancias.index');
    }


}
