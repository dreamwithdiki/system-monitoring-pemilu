<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;
    protected $table = 'sys_partner';
    protected $primaryKey = 'partner_id';

    const CREATED_AT = 'partner_created_date';
    const UPDATED_AT = 'partner_updated_date';

    public $timetamps = true;

    protected $guarded = ['partner_id', 'partner_created_date', 'partner_updated_date'];

    protected $hidden = [
        'partner_created_date',
        'partner_created_by',
        'partner_updated_date',
        'partner_updated_by',
        'partner_deleted_date',
        'partner_deleted_by',
    ];

    public function visit_order()
    {
        return $this->hasMany(VisitOrder::class, 'partner_id');
    }

    public function scopeIsActive($query){
        $query->where('partner_status',2);
    }

    public function province()
    {
        return $this->belongsTo(MasterProvinces::class, 'partner_province', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(MasterRegencies::class, 'partner_regency', 'id');
    }

    public function district()
    {
        return $this->belongsTo(MasterDistricts::class, 'partner_district', 'id');
    }

    public function village()
    {
        return $this->belongsTo(MasterVillages::class, 'partner_village', 'id');
    }
}
