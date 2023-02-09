<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOrder extends Model
{
    use HasFactory;

    protected $primaryKey = 'bill_order_id';
    public $incrementing = false;

    protected $fillable = [
        'bill_order_id',
        'order_id',
        'flavor_id'
    ];

    public function flavor()
    {
        return $this->belongsTo(Flavor::class, 'flavor_id', 'flavor_id')->withTrashed();
    }
}
