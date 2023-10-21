<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataDpt extends Model
{
    use HasFactory;
    protected $table = 'sys_dpt';
    protected $primaryKey = 'dpt_id';

    const CREATED_AT = 'dpt_created_date';
    const UPDATED_AT = 'dpt_updated_date';

    public $timetamps = true;

    protected $guarded = ['dpt_id', 'dpt_created_date', 'dpt_updated_date'];

    protected $hidden = [
        'dpt_created_date',
        'dpt_created_by',
        'dpt_updated_date',
        'dpt_updated_by',
        'dpt_deleted_date',
        'dpt_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('dpt_status',2);
    }

    public function kecamatan_ceklis()
    {
        return $this->hasMany(KecamatanCeklis::class, 'dpt_id');
    }

    public function province()
    {
        return $this->belongsTo(MasterProvinces::class, 'dpt_province', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(MasterRegencies::class, 'dpt_regency', 'id');
    }

    public function district()
    {
        return $this->belongsTo(MasterDistricts::class, 'dpt_district', 'id');
    }

    public function village()
    {
        return $this->belongsTo(MasterVillages::class, 'dpt_village', 'id');
    }

    public function province_name()
    {
        return $this->province->name;
    }

    public function regency_name()
    {
        return $this->regency->name;
    }

    public function district_name()
    {
        return $this->district->name;
    }

    public function village_name()
    {
        return $this->village->name;
    }

    public function tps()
    {
        return $this->belongsTo(DataTps::class, 'tps_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'dpt_created_by', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'dpt_created_by', 'role_id');
    }
}
