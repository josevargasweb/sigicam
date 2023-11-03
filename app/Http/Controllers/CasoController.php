<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use PDF;
use Session;
use TipoUsuario;
use View;
use App\Models\Usuario;


class CasoController extends Controller{

	public function validarCaso(Request $request){
        return $request;
	}

}

?>