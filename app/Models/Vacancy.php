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
        'profession_id',
        'description',
        'requirements',
        'benefits',
        'closing_date',
        'limit',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'vacancy_id');
    }

    public function isLimitReached()
    {
        return $this->applications()->where('status', 'accepted')->count() >= $this->limit;
    }

    public function rejectRemainingApplicants()
    {
        Application::where('vacancy_id', $this->id)
            ->where('status', ['pending', 'interview'])
            ->update(['status' => 'rejected']);
    }

    public function recomServants()
    {
        return $this->hasMany(RecomServant::class, 'vacancy_id');
    }
}
