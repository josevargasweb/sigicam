<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\DerivacionesExtrasistema;
use App\Models\Usuario;
use App\Models\Caso;
use App\Models\Unidad;
use App\Models\EstablecimientosExtraSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

 
class DerivacionExtraSistemaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('role:admin');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_extrasistema' => 'nullable|numeric|min:0',
            'caso' => 'nullable|numeric|min:0',
            'fecha_rescate' => 'nullable|date',
            'fecha' => 'nullable|date',
            'servicio' => 'required|numeric|min:0',
            'usuario' => 'nullable|numeric|min:0',

        ]);


        $nuevo = new DerivacionesExtrasistema();
 
        if(!empty($request->establecimiento_extrasistema)){
             $establecimiento_extrasistema = EstablecimientosExtraSistema::find($request->establecimiento_extrasistema);
             $nuevo->establecimiento_extrasistemas()-> associate($establecimiento_extrasistema); 
        }
        if(!empty($request->caso)){
            $caso = Caso::find($request->caso);
            $nuevo->caso() ->associate($caso);
        }
        if(!empty($request->fecha_rescate)){
            $nuevo->fecha_rescate = $request->fecha_rescate;
        }
        if(!empty($request->fecha)){
            $nuevo->fecha = $request->fecha;
        }
        if(!empty($request->usuario)){
            $usuario = Usuario::find($request->usuario);
            $nuevo->usuario() ->associate ($usuario);        
        }


        $servicio = Unidad::find($request->servicio);
        $nuevo->servicio()->associate($servicio);
        
        $nuevo->save();
        return $nuevo;
    }

    
}
