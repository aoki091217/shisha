<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingSession extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const CONVERSION_STATUS_MARK_CONVERSION = 'mark_conversion';
    public const CONVERSION_STATUSES = [
        self::FRIEND_STATUS_FOLLOWED,
        self::FRIEND_STATUS_UNFOLLOWED,
        self::FRIEND_STATUS_UNKNOWN,
    ];

    protected $fillable = [
        'customer_id',
        'shop_id',
        'visited_at'
    ];

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
