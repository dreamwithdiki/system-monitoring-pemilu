<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'sys_token';
    protected $primaryKey = 'token_id';
    public $timestamps = false;
}
