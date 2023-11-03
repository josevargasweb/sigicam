<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complejidad_servicio;

class ComplejidadservicioController extends Controller
{
    
    public function getComplejidadPorRiesgo(Request $request){

        $complejidad = Complejidad_servicio::where("complejidad","=",$request->complejidad)->get();
        return response()->json($complejidad);
    }
}