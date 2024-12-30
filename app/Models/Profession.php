<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'file_draft',
    ];

    public function servant()
    {
        return $this->hasOne(ServantDetail::class, 'profession_id');
    }

    public function vacancy()
    {
        return $this->hasOne(Vacancy::class, 'profession_id');
    }
}
