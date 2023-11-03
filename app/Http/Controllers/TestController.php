<?php
namespace App\Http\Controllers;
class TestController extends Controller{

	public function doTest(){
        $a = (new EstadisticaPromedioEstada(\Carbon\Carbon::createFromFormat("Y-m-d", "2015-01-01"), \Carbon\Carbon::now()))->get();
		echo "<pre>";
		var_dump($a);
		echo "</pre>";
	}

}
