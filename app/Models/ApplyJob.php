<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplyJob extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'servant_id',
        'vacancy_id',
        'status',
        'notes',
        'interview_date',
        'file_contract',
    ];

    public function servant()
    {
        return $this->belongsTo(User::class, 'servant_id');
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class, 'vacancy_id');
    }
}
