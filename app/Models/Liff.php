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

    public const SHOP_ID = 'shop_id';

    protected $fillable = [
        'liff_id',
        'shop_id',
        'line_token',
        'query'
    ];
}
