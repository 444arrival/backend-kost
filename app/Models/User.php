<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <--- 1. IMPORT TRAIT SANCTUM

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <--- 2. TAMBAHKAN HasApiTokens DI SINI

    /**
     * Kolom yang boleh diisi secara massal (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'no_hp',
        'nomor_kamar',
        'role',
    ];

    /**
     * Kolom yang disembunyikan saat data diubah ke JSON (API)
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
}