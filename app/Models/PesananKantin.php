<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananKantin extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_penghuni',
        'nomor_kamar',
        'total_harga',
        'catatan',
        'status'
    ];

    public function details()
    {
        return $this->hasMany(DetailPesananKantin::class);
    }
}