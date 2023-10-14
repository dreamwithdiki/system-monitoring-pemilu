<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRegencies extends Model
{
    use HasFactory;
    protected $table = 'master_regencies';
    protected $primaryKey = 'id';


    protected $guarded = ['id'];
}
