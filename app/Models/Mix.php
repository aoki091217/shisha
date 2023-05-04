<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mix extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'preset_id',
        'bland_id',
        'flavor_id'
    ];

    public function mixPresets()
    {
        return $this->belongsTo(MixPreset::class, 'preset_id', 'id');
    }

    public function bland()
    {
        return $this->hasOne(Bland::class, 'bland_id', 'bland_id')->withTrashed();
    }

    public function flavor()
    {
        return $this->hasOne(Flavor::class, 'flavor_id', 'flavor_id')->withTrashed();
    }
}
