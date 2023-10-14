<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTps extends Model
{
    use HasFactory;
    protected $table = 'sys_tps';
    protected $primaryKey = 'tps_id';

    const CREATED_AT = 'tps_created_date';
    const UPDATED_AT = 'tps_updated_date';

    public $timetamps = true;

    protected $guarded = ['tps_id', 'tps_created_date', 'tps_updated_date'];

    protected $hidden = [
        'tps_created_date',
        'tps_created_by',
        'tps_updated_date',
        'tps_updated_by',
        'tps_deleted_date',
        'tps_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('tps_status',2);
    }

    public function province()
    {
        return $this->belongsTo(MasterProvinces::class, 'tps_province', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(MasterRegencies::class, 'tps_regency', 'id');
    }

    public function district()
    {
        return $this->belongsTo(MasterDistricts::class, 'tps_district', 'id');
    }

    public function village()
    {
        return $this->belongsTo(MasterVillages::class, 'tps_village', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
