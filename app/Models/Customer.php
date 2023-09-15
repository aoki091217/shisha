<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;

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

    public function getFormatCreatedDateAttribute()
    {
        return Carbon::parse($this->created_date)->format('Y年m月d日 H時i分');
    }

    public function getCheckinDatetimeAttribute()
    {
        return Carbon::parse($this->visited_at)->format('Y年m月d日 H時i分');
    }

    public function scopeSearch($query, $words)
    {
        if (auth()->user()->role_id !== 1) {
            $query->whereHas('customerShops', function ($query) {
                return $query->where('shop_id', auth()->user()->member->shop_id);
            });
        }

        if (isset($words['name'])) {
            return $query->where('name', 'LIKE', "%{$words['name']}%");
        }
        if (isset($words['shop_name'])) {
            return $query->where('shop_name', 'LIKE', "%{$words['shop_name']}%");
        }
    }
}
