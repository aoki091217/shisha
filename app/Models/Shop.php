<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const SHOP_ID = 'shop_id';

    protected $primaryKey = 'shop_id';

    protected $fillable = [
        'name',
        'user_id',
        'account_id',
        'line_token',
        'channel_secret',
        'liff_channel_id',
        'liff_id',
    ];

    protected $hidden = [
        'line_token',
        'channel_secret',
    ];

    public function blands()
    {
        return $this->hasMany(Bland::class, 'shop_id', 'shop_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'shop_id', 'shop_id');
    }

    public function mixes()
    {
        return $this->hasMany(Mix::class, 'shop_id', 'shop_id');
    }

    public function getCreatedDatetimeAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y年m月d日 H時i分');
    }

    public function scopeSearch($query, $words)
    {
        if (isset($words['name'])) {
            return $query->where('name', 'LIKE', "%{$words['name']}%");
        }
    }

    /**
     * LINEのルームに遷移するURI
     * @see: https://developers.line.biz/ja/docs/line-login/using-line-url-scheme/#opening-chat-screen
     * @return string
     */
    public function getRoomUri(): string
    {
        return 'https://line.me/R/ti/p/' . urlencode($this->account_id);
    }

    public function getShopId(): int
    {
        return (int) $this->{self::SHOP_ID};
    }
}
