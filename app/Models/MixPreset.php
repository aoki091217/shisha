<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MixPreset extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'shop_id',
        'name'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($article) {
            $article->mixes()->delete();
        });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id')->withTrashed();
    }

    public function getCreatedDatetimeAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y年m月d日 H時i分');
    }

    public function mixes()
    {
        return $this->hasMany(Mix::class, 'preset_id', 'id');
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

        if (isset($words['name'])) {
            $query->where('name', 'LIKE', "%{$words['name']}%");
        }
    }
}
