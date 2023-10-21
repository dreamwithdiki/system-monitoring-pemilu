<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCaleg extends Model
{
    use HasFactory;
    protected $table = 'sys_caleg';
    protected $primaryKey = 'caleg_id';

    const CREATED_AT = 'caleg_created_date';
    const UPDATED_AT = 'caleg_updated_date';

    public $timetamps = true;

    protected $guarded = ['caleg_id', 'caleg_created_date', 'caleg_updated_date'];

    protected $hidden = [
        'caleg_created_date',
        'caleg_created_by',
        'caleg_updated_date',
        'caleg_updated_by',
        'caleg_deleted_date',
        'caleg_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('caleg_status',2);
    }

    public function kecamatan_ceklis()
    {
        return $this->hasMany(KecamatanCeklis::class, 'caleg_id');
    }
    

    // public function province()
    // {
    //     return $this->belongsTo(MasterProvinces::class, 'caleg_province', 'id');
    // }

    // public function regency()
    // {
    //     return $this->belongsTo(MasterRegencies::class, 'caleg_regency', 'id');
    // }

    // public function district()
    // {
    //     return $this->belongsTo(MasterDistricts::class, 'caleg_district', 'id');
    // }

    // public function village()
    // {
    //     return $this->belongsTo(MasterVillages::class, 'caleg_village', 'id');
    // }
}
