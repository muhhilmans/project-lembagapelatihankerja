<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    protected $fillable = [
        'name',
        'client_data',
        'mitra_data',
        'is_active',
    ];

    protected $casts = [
        'client_data' => 'array',
        'mitra_data' => 'array',
        'is_active' => 'boolean',
    ];
}
