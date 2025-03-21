<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'servant_id',
        'vacancy_id',
        'employe_id',
        'status',
        'notes_interview',
        'salary',
        'notes_verify',
        'notes_accepted',
        'notes_rejected',
        'link_interview',
        'interview_date',
        'work_start_date',
        'work_end_date',
        'file_contract',
        'schema_salary',
    ];

    public function servant()
    {
        return $this->belongsTo(User::class, 'servant_id');
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class, 'vacancy_id');
    }

    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id');
    }

    public function complaint()
    {
        return $this->hasMany(Complaint::class, 'application_id');
    }

    public function workerSalary()
    {
        return $this->hasMany(WorkerSalary::class, 'application_id');
    }

    public function schemaSalary()
    {
        return $this->belongsTo(Salary::class, 'schema_salary');
    }
}
