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

    public const UTM_SOURCE = 'utm_source';
    public const UTM_CAMPAIGN = 'utm_campaign';

    protected $fillable = [
        'line_token',
        'query'
    ];
}
