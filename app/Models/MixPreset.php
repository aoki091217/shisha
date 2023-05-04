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
        'name'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($article) {
            $article->mixes()->delete();
        });
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
        if (isset($words['name'])) {
            $query->where('name', 'LIKE', "%{$words['name']}%");
        }
    }
}
