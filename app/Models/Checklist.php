<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;
    protected $table = 'sys_checklist';
    protected $primaryKey = 'checklist_id';

    const CREATED_AT = 'checklist_created_date';
    const UPDATED_AT = 'checklist_updated_date';

    public $timetamps = true;

    protected $guarded = ['checklist_id', 'checklist_created_date', 'checklist_updated_date'];

    protected $hidden = [
        'checklist_created_date',
        'checklist_created_by',
        'checklist_updated_date',
        'checklist_updated_by',
        'checklist_deleted_date',
        'checklist_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('checklist_status',2);
    }

    public function checklist_group()
    {
        return $this->belongsTo(ChecklistGroup::class, 'checklist_group_id');
    }
}
