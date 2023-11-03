<?php

class Controller extends Controller {
	const ADMIN = 'admin';
	const ADMINSS = 'admin_ss';
	const USUARIO = 'usuario';
	public function __construct(){

		$this->idEstablecimiento = Session::get("idEstablecimiento");
		try{
			$this->tipoUsuario = Auth::user()->tipo;
		}catch(Exception $e){
			
		}
	}
	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}
