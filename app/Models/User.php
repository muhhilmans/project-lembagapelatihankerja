<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, HasRoles;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'email_verified_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function employeDetails()
    {
        return $this->hasOne(EmployeDetail::class, 'user_id');
    }

    public function servantDetails()
    {
        return $this->hasOne(ServantDetail::class, 'user_id');
    }

    public function servantSkills()
    {
        return $this->hasMany(ServantSkill::class, 'user_id');
    }

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class, 'user_id');
    }

    public function appServant()
    {
        return $this->hasMany(Application::class, 'servant_id');
    }

    public function appEmploye()
    {
        return $this->hasMany(Application::class, 'employe_id');
    }

    public function recomServants()
    {
        return $this->hasMany(RecomServant::class, 'servant_id');
    }

    public function complaintEmployes()
    {
        return $this->hasMany(Complaint::class, 'employe_id');
    }

    public function complaintServants()
    {
        return $this->hasMany(Complaint::class, 'servant_id');
    }
}
