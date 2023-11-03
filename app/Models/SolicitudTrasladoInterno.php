<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class SolicitudTrasladoInterno extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "solicitud_traslado_interno";
	protected $primaryKey = "id_solicitud_traslado_interno";

	protected $auditInclude = [
       
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;
}
