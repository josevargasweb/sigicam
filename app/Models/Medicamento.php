<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use OwenIt\Auditing\Contracts\Auditable;


class Medicamento extends Model implements Auditable{
    use \OwenIt\Auditing\Auditable;

    protected $table = "medicamentos";
    protected $primaryKey = "id";
	  public $timestamps = false;

    protected $auditTimestamps = true;
    protected $auditInclude = [];
    protected $auditThreshold = 10;
}

?>
