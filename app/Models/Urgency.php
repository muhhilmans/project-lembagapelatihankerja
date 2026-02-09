<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Urgency extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'name',
        'description',
        'default_urgency',
        'target_role',
        'sla_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
