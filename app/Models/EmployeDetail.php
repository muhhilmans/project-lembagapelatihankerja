<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeDetail extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'address',
        'phone',
        'bank_name',
        'account_number',
        'identity_card',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
