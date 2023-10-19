<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KecamatanCeklis extends Model
{
    use HasFactory;
    protected $table = 'sys_kecamatan_ceklis';
    protected $primaryKey = 'kecamatan_ceklis_id';

    const CREATED_AT = 'kecamatan_ceklis_created_date';

    public $timestamps = false;

    protected $guarded = ['kecamatan_ceklis_id'];

    protected $hidden = [
        'kecamatan_ceklis_created_date',
        'kecamatan_ceklis_created_by',
    ];

    public function checklist_kec()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }
}
