<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Sesion extends Model implements Auditable{

	use \OwenIt\Auditing\Auditable;


	protected $table = "sesion";
	protected $primaryKey = "id_sesion";

	protected $auditInclude = [
       
	];
	protected $fillable = [
		'ultimo_movimiento'
	];

	protected $auditTimestamps = true;
	public $timestamps = false;
    protected $auditThreshold = 10;

}