<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingSession extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const CONVERSION_STATUS_MARK_CONVERSION = 'mark_conversion';
    public const CONVERSION_STATUS_INVALID_FRIEND_STATUS = 'invalid_friend_status';
    public const CONVERSION_STATUS_INVALID_LIFF_STATUS = 'invalid_liff_status';
    public const CONVERSION_STATUS_CUSTOMER_ALREADY_REGISTERED = 'already_registered';
    public const CONVERSION_STATUSES = [
        self::CONVERSION_STATUS_MARK_CONVERSION,
        self::CONVERSION_STATUS_INVALID_FRIEND_STATUS,
        self::CONVERSION_STATUS_INVALID_LIFF_STATUS,
        self::CONVERSION_STATUS_CUSTOMER_ALREADY_REGISTERED,
    ];

    protected $guarded = [
        'id',
    ];

    protected function conversionStatus(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (!in_array($value, self::CONVERSION_STATUSES)) {
                    throw new \InvalidArgumentException('Invalid friend_status');
                }
                return $value;
            },
        );
    }
}
