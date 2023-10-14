<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    use HasFactory;
    protected $table = 'sys_debtor';
    protected $primaryKey = 'debtor_id';

    const CREATED_AT = 'debtor_created_date';
    const UPDATED_AT = 'debtor_updated_date';

    public $timetamps = true;

    protected $guarded = ['debtor_id', 'debtor_created_date', 'debtor_updated_date'];

    protected $hidden = [
        'debtor_created_date',
        'debtor_created_by',
        'debtor_updated_date',
        'debtor_updated_by',
        'debtor_deleted_date',
        'debtor_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('debtor_status',2);
    }

    public function province()
    {
        return $this->belongsTo(MasterProvinces::class, 'debtor_province', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(MasterRegencies::class, 'debtor_regency', 'id');
    }

    public function district()
    {
        return $this->belongsTo(MasterDistricts::class, 'debtor_district', 'id');
    }

    public function village()
    {
        return $this->belongsTo(MasterVillages::class, 'debtor_village', 'id');
    }

}
