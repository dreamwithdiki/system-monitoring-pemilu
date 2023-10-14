<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'sys_user';
    protected $primaryKey = 'user_id';
	
    const CREATED_AT = 'user_created_date';
    const UPDATED_AT = 'user_updated_date';

    public $timestamps = true;

    // protected $guarded = ['user_id', 'user_created_date', 'user_updated_date'];
    // protected $guard_name = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'user_firstname', 'user_lastname', 'user_password',
    // ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'user_password',
        'user_created_date',
        'user_created_by',
        'user_updated_date',
        'user_updated_by',
        'user_deleted_date',
        'user_deleted_by', 
    ];

    public function getAuthPassword()
    {
        return $this->user_password;
    }

    public function scopeIsActive($query){
        $query->where('user_status',2);
    }

    public function client_contact()
    {
        return $this->belongsTo(ClientContact::class, 'user_ref_id');
    }

    public static function getpermissionGroups()
    {
        $permission_groups = DB::table('permissions')
            ->select('group_name as name')
            ->groupBy('group_name')
            ->get();
        return $permission_groups;
    }

    public static function getpermissionsByGroupName($group_name)
    {
        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->get();
        return $permissions;
    }

    public static function roleHasPermissions($role, $permissions)
    {
        $hasPermission = true;
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                $hasPermission = false;
                return $hasPermission;
            }
        }
        return $hasPermission;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function province()
    {
        return $this->belongsTo(MasterProvinces::class, 'user_province', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(MasterRegencies::class, 'user_regency', 'id');
    }

    public function district()
    {
        return $this->belongsTo(MasterDistricts::class, 'user_district', 'id');
    }

    public function village()
    {
        return $this->belongsTo(MasterVillages::class, 'user_village', 'id');
    }
}
