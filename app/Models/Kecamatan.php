<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;
    protected $table = 'sys_kecamatan';
    protected $primaryKey = 'kecamatan_id';

    const CREATED_AT = 'kecamatan_created_date';
    const UPDATED_AT = 'kecamatan_updated_date';

    public $timetamps = true;

    protected $guarded = ['kecamatan_id', 'kecamatan_created_date', 'kecamatan_updated_date'];

    protected $hidden = [
        'kecamatan_created_date',
        'kecamatan_created_by',
        'kecamatan_updated_date',
        'kecamatan_updated_by',
        'kecamatan_deleted_date',
        'kecamatan_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('kecamatan_status',2);
    }
}
