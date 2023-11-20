<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property
 */
class Liff extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_token',
        'query'
    ];
}
