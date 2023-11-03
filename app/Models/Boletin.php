<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

use DB;
use Auth;
use Log;

class Boletin extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "boletines";
    protected $primaryKey = "id";

    protected $auditInclude = [
       
    ];
    protected $auditTimestamps = true;
    protected $auditThreshold = 10;

    /* public function productos(){
        return $this->hasMany(Producto::class);
    } */

    public function boletinesProductos(){
        return $this->belongsToMany(Producto::class, "boletin_producto", "id_boletin", "id_producto");
    }


}
