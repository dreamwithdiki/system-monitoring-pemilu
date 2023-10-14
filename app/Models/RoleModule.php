<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModule extends Model
{
    use HasFactory;
    protected $table = 'sys_role_module';
    protected $primaryKey = 'role_module_id';

    const CREATED_AT = 'role_module_created_date';
    const UPDATED_AT = 'role_module_updated_date';

    public $timetamps = true;

    protected $guarded = ['role_module_id', 'role_module_created_date', 'role_module_updated_date'];

    protected $hidden = [
        'role_module_created_date',
        'role_module_created_by',
        'role_module_updated_date',
        'role_module_updated_by',
        'role_module_deleted_date',
        'role_module_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('role_module_status',2);
    }
}
