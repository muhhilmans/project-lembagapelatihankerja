<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecomServant extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'vacancy_id',
        'servant_id'
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function servant()
    {
        return $this->belongsTo(User::class);
    }
}
