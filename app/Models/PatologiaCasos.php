<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatologiaCasos extends Model
{
    protected $table = "patologia_casos";
	public $timestamps = false;
    protected $primaryKey = "id";
}