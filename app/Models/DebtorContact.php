<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtorContact extends Model
{
    use HasFactory;
    protected $table = 'sys_debtor_contact';
    protected $primaryKey = 'debtor_contact_id';

    const CREATED_AT = 'debtor_contact_created_date';
    const UPDATED_AT = 'debtor_contact_updated_date';

    public $timetamps = true;

    protected $guarded = ['debtor_contact_id', 'debtor_contact_created_date', 'debtor_contact_updated_date'];

    protected $hidden = [
        'debtor_contact_created_date',
        'debtor_contact_created_by',
        'debtor_contact_updated_date',
        'debtor_contact_updated_by',
        'debtor_contact_deleted_date',
        'debtor_contact_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('debtor_contact_status',2);
    }
}
