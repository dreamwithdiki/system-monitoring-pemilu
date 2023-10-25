<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;
    protected $table = 'sys_pengaduan';
    protected $primaryKey = 'pengaduan_id';

    const CREATED_AT = 'pengaduan_created_date';
    const UPDATED_AT = 'pengaduan_updated_date';

    public $timetamps = true;

    protected $guarded = ['pengaduan_id', 'pengaduan_created_date', 'pengaduan_updated_date'];

    protected $hidden = [
        'pengaduan_created_date',
        'pengaduan_created_by',
        'pengaduan_updated_date',
        'pengaduan_updated_by',
        'pengaduan_deleted_date',
        'pengaduan_deleted_by',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'pengaduan_created_by', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function history()
    {
        return $this->hasMany(PengaduanHistory::class, 'pengaduan_id');
    }
}
