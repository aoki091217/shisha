<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'line_token',
        'name',
        'step'
    ];

    public function customerShops()
    {
        return $this->hasMany(CustomerShop::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function isDeleted(): bool
    {
        return !is_null($this->deleted_at);
    }

    public function getFormatCreatedDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y年m月d日 H時i分');
    }

    public function getCheckinDatetimeAttribute()
    {
        return Carbon::parse($this->visited_at)->format('Y年m月d日 H時i分');
    }

    public function scopeSearch(Builder $query, $words)
    {
        if (auth()->user()->role_id !== 1) {
            $query->whereHas('customerShops', function ($query) {
                return $query->where('shop_id', auth()->user()->member->shop_id);
            });
        }

        if (!empty($words['name'])) {
            $query->where('customers.name', 'LIKE', "%{$words['name']}%");
        }
        if (!empty($words['shop_id'])) {
            $query->where('shops.shop_id', (int) $words['shop_id']);
        }
        if (!empty($words['visited_date'])) {
            $query->whereRaw("DATE_FORMAT(visited_at, '%Y-%m-%d') = '{$words['visited_date']}'");
        }
    }
}
