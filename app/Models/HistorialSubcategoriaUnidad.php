<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class HistorialSubcategoriaUnidad extends Model{
	
    protected $table = "historial_unidad_subcategoria";
    protected $primaryKey = "id";
	public $timestamps = false;
}

?>