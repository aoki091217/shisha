<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerShop extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'shop_id',
        'visited_at'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCheckinDatetimeAttribute()
    {
        return Carbon::parse($this->visited_at)->format('Y年m月d日 H時i分');
    }

    public function getVisitedAt(): Carbon
    {
        return Carbon::parse($this->visited_at);
    }

    public function moreThanHalfDay(): bool
    {
        return now()->diffInHours($this->getVisitedAt()) >= 12;
    }

    public function scopeSearch($query, $words)
    {
        if (isset($words['name'])) {
            $query->whereHas('customer', function ($query) use ($words) {
                $query->where('name', 'LIKE', "%{$words['name']}%");
            });
        }

        if (isset($words['shop_id'])) {
            $query->where('shop_id', $words['shop_id']);
        }

        if (isset($words['visited_date'])) {
            $query->whereRaw("DATE_FORMAT(visited_at, '%Y-%m-%d') = '{$words['visited_date']}'");
        }

        if (isset($words['visited_hour']) && isset($words['visited_time'])) {
            $query->whereRaw("DATE_FORMAT(visited_at, '%H:%i') = '{$words['visited_hour']}:{$words['visited_minute']}'");
        }

        if (isset($words['visited_hour'])) {
            $query->whereRaw("DATE_FORMAT(visited_at, '%H') = '{$words['visited_hour']}'");
        }
        if (isset($words['visited_minute'])) {
            $query->whereRaw("DATE_FORMAT(visited_at, '%i') = '{$words['visited_minute']}'");
        }
    }
}
