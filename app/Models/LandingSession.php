<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSession extends Model
{
    use HasFactory;

    public const CONVERSION_STATUS_MARK_CONVERSION = 'mark_conversion';
    public const CONVERSION_STATUS_INVALID_FRIEND_STATUS = 'invalid_friend_status';
    public const CONVERSION_STATUS_ALREADY_ACTIVATED = 'already_activated';
    public const CONVERSION_STATUS_UNKNOWN = 'unknown';
    public const CONVERSION_STATUSES = [
        self::CONVERSION_STATUS_MARK_CONVERSION,
        self::CONVERSION_STATUS_INVALID_FRIEND_STATUS,
        self::CONVERSION_STATUS_ALREADY_ACTIVATED,
        self::CONVERSION_STATUS_UNKNOWN,
    ];

    protected $guarded = [
        'id',
    ];

    public $casts = [
        'parameters' => 'array',
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

    /**
     * LIFF認証後のコールバックページのURL
     * @return string
     */
    public function callbackUrl(): string
    {
        return route('liff.callback', [
            'shop_id' => $this->shop_id,
            'session_token' => $this->session_token,
        ]);
    }
}
