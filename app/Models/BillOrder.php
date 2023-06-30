<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'mix_id'
    ];

    public function mix()
    {
        return $this->belongsTo(MixPreset::class)->withTrashed();
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
}
