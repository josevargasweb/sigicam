<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
//use response;


class horaController extends Controller
{
    public function testCambioHora(){

        $horaPostgres = DB::select(DB::raw("Select now()"));
        $carbon =  \Carbon\Carbon::now();
        $php =  date("H:i:s");

        return response()->json(array("postgres"=>$horaPostgres, "carbon"=>$carbon, "php"=>$php));

    }
}
