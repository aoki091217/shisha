<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'bill_id';

    protected $fillable = [
        'amount',
        'shop_id',
        'member_id',
        'share',
        'top_change',
        'bill_date',
        'is_draft'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id')->withTrashed();
    }

    public function billCustomers()
    {
        return $this->hasMany(BillCustomer::class, 'bill_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id')->withTrashed();
    }

    public function billOrders()
    {
        return $this->hasMany(BillOrder::class, 'bill_id');
    }

    public function getShareNameAttribute()
    {
        if ($this->share == 1) {
            return '有';
        } else {
            return '無';
        }
    }

    public function getBillDatetimeAttribute()
    {
        $datetime = Carbon::parse($this->bill_date);
        return sprintf('%s (%s)', $datetime->format('Y年m月d日 H時i分'), $datetime->isoFormat('ddd'));
    }

    public function getBillDayAttribute()
    {
        return Carbon::parse($this->bill_date)->toDateString();
    }

    public function getBillTimeAttribute()
    {
        return Carbon::parse($this->bill_date)->toTimeString();
    }

    public function getCreatedDatetimeAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y年m月d日 H時i分');
    }

    public function scopeSearch($query, $words)
    {
        if (auth()->user()->role_id !== 1) {
            $query->where('shop_id', auth()->user()->member->shop_id);
        }

        if (!empty($words['shop_id'])) {
            $query->where('shop_id', $words['shop_id']);
        }

        if (isset($words['is_period']) && isset($words['end_date'])) {
            $query->whereRaw("CAST(bill_date AS DATE) >= '{$words['start_date']}'")
                ->whereRaw("CAST(bill_date AS DATE) <= '{$words['end_date']}'");
        } elseif (!empty($words['start_date'])) {
            $query->whereRaw("CAST(bill_date AS DATE) = '{$words['start_date']}'");
        }

        return $query;
    }
}
