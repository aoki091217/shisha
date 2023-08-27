<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'shop_id',
        'user_id',
        'name'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($article) {
            $article->user()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id')->withTrashed();
    }

    public function getShopNameAttribute()
    {
        return $this->shop->name;
    }

    public function getCreatedDatetimeAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y年m月d日 H時i分');
    }

    public function scopeSearch($query, $words)
    {
        if (auth()->user()->role_id !== 1) {
            $query->where('shop_id', auth()->user()->member->shop_id);
        }

        if (isset($words['shop_id'])) {
            $query->where('shop_id', $words['shop_id']);
        }

        if (isset($words['name'])) {
            $query->where('name', 'LIKE', "%{$words['name']}%");
        }

        return $query;
    }
}
