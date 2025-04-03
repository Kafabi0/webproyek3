<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;  // Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject  // Implementasikan JWTSubject
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Implementasi method dari JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();  // Biasanya kita menggunakan ID pengguna sebagai identifier
    }

    public function getJWTCustomClaims()
    {
        return [];  // Anda bisa menambahkan klaim khusus di sini, jika diperlukan
    }
}
