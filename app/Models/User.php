<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function otpCode()
    {
        return $this->hasOne(Otp::class, 'user_id');
    }

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

    public function reportedPengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'reported_user_id');
    }

    public function reporterPengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'reporter_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'user_id');
    }

    public function favoriteVacancies()
    {
        return $this->belongsToMany(Vacancy::class, 'favorite_vacancies', 'user_id', 'vacancy_id')
            ->with('user', 'profession')
            ->withTimestamps();
    }

    public function favoriteServants()
    {
        return $this->belongsToMany(
            User::class,
            'favorite_servants',
            'employe_detail_id',
            'servant_detail_id'
        )->withTimestamps();
    }

    public function favoritedByEmployers()
    {
        return $this->belongsToMany(
            User::class,
            'favorite_servants',
            'servant_detail_id',
            'employe_detail_id'
        )->withTimestamps();
    }

    // Relasi untuk review yang DITERIMA user ini
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    // Mengambil rata-rata rating (Otomatis hitung, baik dia Majikan atau Pembantu)
    public function getAverageRatingAttribute()
    {
        return round($this->receivedReviews()->avg('rating'), 1) ?? 0;
    }

    // Atribut Virtual untuk menghitung jumlah ulasan
    public function getReviewCountAttribute()
    {
        return $this->receivedReviews()->count();
    }
}
