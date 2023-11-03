<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\PixysRemoto;
use App\Models\PixysLocal;
use \Carbon;

class PyxisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tabla = DB::select('SELECT * FROM integracion');

        //dd($tabla);
        return view('/Pyxis/integracion')->with('tabla',$tabla);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function transaction(){
        
        

            $pixysLocal = PixysLocal::where("dato_enviado","false")->get();


            foreach($pixysLocal as $pixys){
                $pixysRemoto = new PixysRemoto;
                //$pixysRemoto
                $pixysRemoto->id_paciente = $pixys->id_paciente;
                $pixysRemoto->id_alt_paciente_run = $pixys->id_alt_paciente_run;
                $pixysRemoto->id_alt_paciente_hc = $pixys->id_alt_paciente_hc;
                
                $pixysRemoto->nombre_paciente = $pixys->nombre_paciente;

                $pixysRemoto->unidad_enfermeria = $pixys->unidad_enfermeria;

                $pixysRemoto->sala = $pixys->sala;
                $pixysRemoto->cama = $pixys->cama;

                $pixysRemoto->fecha_admision = $pixys->fecha_admision;
                $pixysRemoto->mensaje = $pixys->mensaje;
                $pixysRemoto->estado = $pixys->estado;

		        $carbon = new \Carbon\Carbon();

		        $inserccion = $carbon::parse($pixys->fecha_inserccion);
                $pixysRemoto->fecha_insercion = $carbon->parse($pixys->fecha_inserccion)->format('Y-d-m H:i:s');

                $pixysRemoto->fecha_lectura = $carbon::parse($pixys->fecha_lectura)->format('Y-d-m H:i:s');
                $pixysRemoto->maquina = $pixys->maquina;

    
                //actualizar para saber sque datos ya se enviaron
		        $pixys->dato_enviado = true;  
              $pixys->save();
                

                $pixysRemoto->save();
            }

        

            return "OK";

        

        
        //$pixis = PixysRemoto::get();
        
    }

    public function getDataPixysRemoto(){
        

//	PixysRemoto::truncate();
        $data = PixysRemoto::count();

        return $data;
    }


}
