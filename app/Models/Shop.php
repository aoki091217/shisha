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

    protected $primaryKey = 'shop_id';

    protected $fillable = [
        'name',
        'user_id',
        'account_id',
        'line_token',
        'channel_secret'
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'shop_id', 'shop_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
