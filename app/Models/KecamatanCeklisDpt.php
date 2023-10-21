<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KecamatanCeklisDpt extends Model
{
    use HasFactory;
    protected $table = 'sys_kecamatan_ceklis_dpt';
    protected $primaryKey = 'kecamatan_ceklis_dpt_id';

    const CREATED_AT = 'kecamatan_ceklis_dpt_created_date';

    public $timestamps = false;

    protected $guarded = ['kecamatan_ceklis_dpt_id'];

    protected $hidden = [
        'kecamatan_ceklis_dpt_created_date',
        'kecamatan_ceklis_dpt_created_by',
    ];

    public function checklist_kec()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }
    
    public function kecamatan_name()
    {
        return $this->checklist_kec->kecamatan_name;
    }

}
