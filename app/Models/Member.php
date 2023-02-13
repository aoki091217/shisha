<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'member_id';

    protected $fillable = [
        'shop_id',
        'name'
    ];

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
        if (isset($words['shop_id'])) {
            $query->where('shop_id', $words['shop_id']);
        }

        if (isset($words['name'])) {
            $query->where('name', 'LIKE', "%{$words['name']}%");
        }

        return $query;
    }
}
