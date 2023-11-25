<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flavor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'flavor_id';

    protected $fillable = [
        'bland_id',
        'shop_id',
        'name'
    ];

    public function bland()
    {
        return $this->belongsTo(Bland::class, 'bland_id', 'bland_id')->withTrashed();
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id')->withTrashed();
    }

    public function getCreatedDatetimeAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y年m月d日 H時i分');
    }

    public function scopeSearch($query, $words)
    {
        if (auth()->user()->role_id !== 1) {
            $query->where('shop_id', auth()->user()->member->shop_id);
        } else {
            if (!empty($words['shop_id'])) {
                $query->where('shop_id', $words['shop_id']);
            }
        }

        if (!empty($words['bland_name'])) {
            $query->whereHas('bland', function ($query) use ($words) {
                $query->where('name', 'LIKE', "%{$words['bland_name']}%");
            });
        }

        if (!empty($words['name'])) {
            $query->where('name', 'LIKE', "%{$words['name']}%");
        }

        return $query;
    }
}
