<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Garansi extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'max_replacements',
        'price',
        'is_active',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class, 'garansi_id', 'id');
    }
}
