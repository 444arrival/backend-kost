<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesananKantin extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_kantin_id',
        'menu_id',
        'nama_menu',
        'quantity',
        'harga'
    ];

    public function pesanan()
    {
        return $this->belongsTo(PesananKantin::class);
    }
}
