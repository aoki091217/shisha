<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerShop extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_token',
        'shop_id',
        'visit'
    ];
}
