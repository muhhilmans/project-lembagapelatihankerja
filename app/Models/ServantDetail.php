<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServantDetail extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'marital_status',
        'children',
        'profession_id',
        'last_education',
        'phone',
        'emergency_number',
        'address',
        'rt',
        'rw',
        'village',
        'district',
        'regency',
        'province',
        'photo',
        'identity_card',
        'family_card',
        'working_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }
}
