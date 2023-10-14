<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $table = 'sys_module';
    protected $primaryKey = 'module_id';

    const CREATED_AT = 'module_created_date';
    const UPDATED_AT = 'module_updated_date';

    public $timetamps = true;

    protected $guarded = ['module_id', 'module_created_date', 'module_updated_date'];

    protected $hidden = [
        'module_created_date',
        'module_created_by',
        'module_updated_date',
        'module_updated_by',
        'module_deleted_date',
        'module_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('module_status',2);
    }
}
