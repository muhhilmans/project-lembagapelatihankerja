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
        'absence',
        'absence_reason',
        'extra_deduction',
        'quantity',
        'total_salary',
        'total_salary_majikan',
        'total_salary_pembantu',
        'status',
        'payment_majikan_image',
        'payment_majikan_amount',
        'payment_majikan_method',
        'payment_majikan_ref_number',
        'payment_majikan_status',
        'payment_majikan_verified_at',
        'payment_pembantu_image',
        'payment_pembantu_amount',
        'payment_pembantu_ref_number',
        'payment_pembantu_status',
        'payment_pembantu_transfer_at',
        'voucher_id',
    ];

    protected $casts = [
        'month' => 'date',
        'payment_majikan_verified_at' => 'datetime',
        'payment_pembantu_transfer_at' => 'datetime',
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
