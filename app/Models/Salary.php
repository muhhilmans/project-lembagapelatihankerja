<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'adds_client',
        'bpjs_client',
        'adds_mitra',
        'bpjs_mitra',
    ];

    public function application()
    {
        return $this->hasOne(Application::class, 'schema_salary', 'id');
    }
}
