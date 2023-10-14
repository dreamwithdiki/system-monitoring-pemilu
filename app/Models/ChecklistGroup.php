<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistGroup extends Model
{
    use HasFactory;
    protected $table = 'sys_checklist_group';
    protected $primaryKey = 'checklist_group_id';

    const CREATED_AT = 'checklist_group_created_date';
    const UPDATED_AT = 'checklist_group_updated_date';

    public $timetamps = true;

    protected $guarded = ['checklist_group_id', 'checklist_group_created_date', 'checklist_group_updated_date'];

    protected $hidden = [
        'checklist_group_created_date',
        'checklist_group_created_by',
        'checklist_group_updated_date',
        'checklist_group_updated_by',
        'checklist_group_deleted_date',
        'checklist_group_deleted_by',
    ];

    public function scopeIsActive($query){
        $query->where('checklist_group_status',2);
    }

    public function checklist()
    {
        return $this->hasMany(Checklist::class, 'checklist_group_id');
    }

    public function checklist_active()
    {
        return $this->hasMany(Checklist::class, 'checklist_group_id')->isActive();
    }
}
