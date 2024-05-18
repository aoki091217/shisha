<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class CustomerShopStatus extends Model
{
    public const FRIEND_STATUS_FOLLOWED = 'followed';
    public const FRIEND_STATUS_UNFOLLOWED = 'unfollowed';
    public const FRIEND_STATUS_UNKNOWN = 'unknown';
    public const FRIEND_STATUSES = [
        self::FRIEND_STATUS_FOLLOWED,
        self::FRIEND_STATUS_UNFOLLOWED,
        self::FRIEND_STATUS_UNKNOWN,
    ];

    public const LIFF_STATUS_ACTIVE = 'active';
    public const LIFF_STATUS_INACTIVE = 'inactive';
    public const LIFF_STATUS_UNKNOWN = 'unknown';
    public const LIFF_STATUSES = [
        self::LIFF_STATUS_ACTIVE,
        self::LIFF_STATUS_INACTIVE,
        self::LIFF_STATUS_UNKNOWN,
    ];

    protected $fillable = [
        'shop_id',
        'customer_id',
        'friend_status',
        'liff_status',
        'activated_at',
        'first_visited_at',
        'recently_visited_at',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected function friendStatus(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (!in_array($value, self::FRIEND_STATUSES)) {
                    throw new \InvalidArgumentException('Invalid friend_status');
                }
                return $value;
            },
        );
    }

    protected function liffStatus(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (!in_array($value, self::LIFF_STATUSES)) {
                    throw new \InvalidArgumentException('Invalid liff_status');
                }
                return $value;
            },
        );
    }
}
