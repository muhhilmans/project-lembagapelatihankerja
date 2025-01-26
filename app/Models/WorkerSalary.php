<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerSalary extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'application_id',
        'month',
        'presence',
        'total_salary',
        'total_salary_majikan',
        'total_salary_pembantu',
        'status',
        'payment_majikan_image',
        'payment_pembantu_image',
        'voucher_id',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
