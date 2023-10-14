<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterVillages extends Model
{
    use HasFactory;
    protected $table = 'master_villages';
    protected $primaryKey = 'id';


    protected $guarded = ['id'];
}
