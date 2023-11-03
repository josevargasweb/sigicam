<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;

use Illuminate\Database\Eloquent\Model;
use Log;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class DocumentosDiptico extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "documentos_diptico";
	public $timestamps = false;
    protected $primaryKey = "id";

    protected $auditInclude = [
       
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;
}