<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'produk_id', 'order_id', 'status', 'total_price', 'address', 'phone_number',
    ];
}
