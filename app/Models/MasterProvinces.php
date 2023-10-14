<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProvinces extends Model
{
    use HasFactory;
    protected $table = 'master_provinces';
    protected $primaryKey = 'id';


    protected $guarded = ['id'];
}
