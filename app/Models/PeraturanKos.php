<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeraturanKos extends Model
{
    use HasFactory;

    protected $table = 'peraturan_kos';
    protected $fillable = ['judul', 'isi_poin'];
}