<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengaduan';

    protected $fillable = [
        'contract_id',
        'complaint_type_id',
        'urgency_level',
        'reporter_id',
        'reported_user_id',
        'description',
        'status',
        'applied_sanction_id',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function complaintType()
    {
        return $this->belongsTo(Urgency::class, 'complaint_type_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'contract_id');
    }
}
