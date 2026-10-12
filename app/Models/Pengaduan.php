<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kategori',
        'judul',
        'deskripsi',
        'status',
    ];

    // Relasi ke user (penghuni)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}