<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistAnswer extends Model
{
    use HasFactory;
    protected $table = 'sys_checklist_answer';
    protected $primaryKey = 'checklist_answer_id';

    const CREATED_AT = 'checklist_answer_created_date';
    const UPDATED_AT = 'checklist_answer_updated_date';

    public $timetamps = true;

    protected $guarded = ['checklist_answer_id', 'checklist_answer_created_date', 'checklist_answer_updated_date'];

    protected $hidden = [
        'checklist_answer_created_date',
        'checklist_answer_created_by',
        'checklist_answer_updated_date',
        'checklist_answer_updated_by',
        'checklist_answer_deleted_date',
        'checklist_answer_deleted_by',
    ];
}
