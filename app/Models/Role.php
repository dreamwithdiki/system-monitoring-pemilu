<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'sys_role';
    protected $primaryKey = 'role_id';

    const CREATED_AT = 'role_created_date';
    const UPDATED_AT = 'role_updated_date';

    public $timetamps = true;

    protected $guarded = ['role_id', 'role_created_date', 'role_updated_date'];

    protected $hidden = [
        'role_created_date',
        'role_created_by',
        'role_updated_date',
        'role_updated_by',
        'role_deleted_date',
        'role_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('role_status',2);
    }
}
