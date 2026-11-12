<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Model extends Eloquent
{
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
