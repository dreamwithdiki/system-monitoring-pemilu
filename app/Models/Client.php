<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'sys_client';
    protected $primaryKey = 'client_id';

    const CREATED_AT = 'client_created_date';
    const UPDATED_AT = 'client_updated_date';

    public $timetamps = true;

    protected $guarded = ['client_id', 'client_created_date', 'client_updated_date'];

    protected $hidden = [
        'client_created_date',
        'client_created_by',
        'client_updated_date',
        'client_updated_by',
        'client_deleted_date',
        'client_deleted_by',
    ];

    public function client_order()
    {
        return $this->hasMany(VisitOrder::class, 'client_id');
    }

    public function scopeIsActive($query){
        $query->where('client_status',2);
    }
}
