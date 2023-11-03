<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;



class DocumentoDerivacionCaso extends Model implements Auditable{
    use \OwenIt\Auditing\Auditable;
    
	protected $table = "documento_derivacion_caso";
    protected $primaryKey = "id_documento_derivacion_caso";

	protected $auditInclude = [
       
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;
	 
}

