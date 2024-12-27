<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'application_id',
        'servant_id',
        'employe_id',
        'message',
        'status',
        'file',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id');
    }

    public function servant()
    {
        return $this->belongsTo(User::class, 'servant_id');
    }
}
