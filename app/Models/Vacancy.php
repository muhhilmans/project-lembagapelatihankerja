<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'requirements',
        'benefits',
        'closing_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applyJobs()
    {
        return $this->hasMany(ApplyJob::class, 'vacancy_id');
    }
}