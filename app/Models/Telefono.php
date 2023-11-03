<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;
use Log;
class Telefono extends Model {
    protected $table = "telefonos";
    protected $primaryKey = "id";
    public $timestamps = false;
}