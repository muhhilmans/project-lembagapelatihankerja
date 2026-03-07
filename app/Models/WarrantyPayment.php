<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyPayment extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'application_id',
        'month_number',
        'month_date',
        'amount',
        'payment_image',
        'status',
        'verified_at',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
