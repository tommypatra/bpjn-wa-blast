<?php

namespace App\Models;

use App\Models\WaPesan;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    public function wapesan()
    {
        return $this->hasMany(WaPesan::class);
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function proses()
    {
        return $this->hasMany(Proses::class);
    }

    public function kirimpesan()
    {
        return $this->hasMany(KirimPesan::class);
    }
}
