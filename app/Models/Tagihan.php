<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $fillable = [
        'user_id', 'kamar', 'periode', 'nominal',
        'jatuh_tempo', 'status', 'order_id', 'dibayar_pada',
    ];
}