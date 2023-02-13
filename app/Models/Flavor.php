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
        'name'
    ];

    public function bland()
    {
        return $this->belongsTo(Bland::class, 'bland_id', 'bland_id')->withTrashed();
    }

    public function scopeSearch($query, $words)
    {
        if (isset($words['bland_id'])) {
            $query->where('bland_id', $words['bland_id']);
        }

        if (isset($words['name'])) {
            $query->where('name', 'LIKE', "%{$words['name']}%");
        }

        return $query;
    }
}
