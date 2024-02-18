<?php

namespace App\Repositories;

use App\Http\Requests\ReportRequest;
use App\Models\Customer;
use App\Models\Liff;
use App\Models\Shop;
use App\Services\LiffService;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;

class ReportRepository
{
    public function __construct(
        private LiffService $liffService
    ) {}

    public function getCodeClickCount(ReportRequest $reportRequest): Collection
    {
        // $query = $reportRequest->getHash();
        $today = Carbon::today();
        // if (isset($reportRequest['start_date'])) {

        // }

        return Liff::whereBetween('created_at', [
                $today->copy()->startOfYear()->toDateTimeString(),
                $today->copy()->endOfYear()->toDateTimeString()
            ])->get();
    }

    public function getFollowerCount(ReportRequest $reportRequest)
    {
        $shopId = isset($reportRequest['shop_id']) ? $reportRequest['shop_id'] : Shop::first()->shop_id ;

        if (auth()->user()->role_id === 1) {
            return Customer::with('customerShops')->whereHas('customerShops', function ($query) use ($shopId) {
                return $query->where('shop_id', $shopId);
            })->whereNull('deleted_at')->get();
        } else {
            return Customer::with('customerShops')->whereHas('customerShops', function ($query) {
                return $query->where('shop_id', auth()->user()->member->shop_id);
            })->whereNull('deleted_at')->get();
        }
    }

    public function getBlockCount()
    {
        return ;
    }

    public function getUsedCouponCount()
    {
        return ;
    }

    public function getVisitedCount()
    {
        return ;
    }
}
