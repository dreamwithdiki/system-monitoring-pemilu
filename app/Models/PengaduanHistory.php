<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaduanHistory extends Model
{
    use HasFactory;
    protected $table = 'sys_pengaduan_history';
    protected $primaryKey = 'pengaduan_history_id';

    protected $fillable = [
        'pengaduan_id',
        'pengaduan_status',
        'pengaduan_history_desc',
        'pengaduan_history_created_by',
        'pengaduan_history_created_date',
    ];
    
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'pengaduan_history_created_by');
    }
}
