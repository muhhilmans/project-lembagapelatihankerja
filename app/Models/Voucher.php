<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code',
        'people_used',
        'time_used',
        'expired_date',
        'discount',
        'is_active',
    ];

    public function workerSalary()
    {
        return $this->hasMany(WorkerSalary::class);
    }
}
