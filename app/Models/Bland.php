<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bland extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'bland_id';

    protected $fillable = [
        'name'
    ];

    public function flavors()
    {
        return $this->hasMany(Flavor::class, 'bland_id', 'bland_id');
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
