<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $table = 'sys_site';
    protected $primaryKey = 'site_id';

    const CREATED_AT = 'site_created_date';
    const UPDATED_AT = 'site_updated_date';

    public $timetamps = true;

    protected $guarded = ['site_id', 'site_created_date', 'site_updated_date'];

    protected $hidden = [
        'site_created_date',
        'site_created_by',
        'site_updated_date',
        'site_updated_by',
        'site_deleted_date',
        'site_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('site_status',2);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function province()
    {
        return $this->belongsTo(MasterProvinces::class, 'site_province', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(MasterRegencies::class, 'site_regency', 'id');
    }

    public function district()
    {
        return $this->belongsTo(MasterDistricts::class, 'site_district', 'id');
    }

    public function village()
    {
        return $this->belongsTo(MasterVillages::class, 'site_village', 'id');
    }
}
