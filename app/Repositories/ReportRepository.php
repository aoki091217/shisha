<?php

namespace App\Repositories;

use App\Http\Requests\ReportRequest;
use App\Models\Code;
use App\Models\Customer;
use App\Models\CustomerShop;
use App\Models\Liff;
use App\Models\Shop;
use App\Services\LiffService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ReportRepository
{
    public function __construct(
        private LiffService $liffService,
        private CodeRepository $codeRepository
    ) {}

    public function getCodeClickCount(ReportRequest $reportRequest, Collection $shops): array
    {
        $period = $this->liffService->getPeriodReport($reportRequest);

        foreach (range($period['start_year'], $period['end_year']) as $year) {
            foreach ($shops as $i => $shop) {
                $result[$year][$i]['shop'] = $shop;

                foreach (range(1, 12) as $month) {
                    $startMonth = Carbon::createFromDate($year, $month)->startOfMonth()->startOfDay();
                    $endMonth = Carbon::createFromDate($year, $month)->endOfMonth()->endOfDay();

                    $query = Liff::query();
                    $query->where(Liff::SHOP_ID, $shop->shop_id)
                        ->whereBetween(Liff::CREATED_AT, [$startMonth, $endMonth]);

                    if (isset($reportRequest[ReportRequest::HASH]) && !is_null($reportRequest[ReportRequest::HASH])) {
                        $code = $this->codeRepository->findByHash($reportRequest->getHash());
                        $query->where(ReportRequest::QUERY, $code->getParameter());
                    }

                    $result[$year][$i]['data'][$month] = $query->count();
                }
            }
        }

        return $result;
    }

    public function getFollowerCount(ReportRequest $reportRequest, Collection $shops): array
    {
        $period = $this->liffService->getPeriodReport($reportRequest);

        foreach (range($period['start_year'], $period['end_year']) as $year) {
            foreach ($shops as $i => $shop) {
                $result[$year][$i]['shop'] = $shop;

                foreach (range(1, 12) as $month) {
                    $startMonth = Carbon::createFromDate($year, $month)->startOfMonth()->startOfDay();
                    $endMonth = Carbon::createFromDate($year, $month)->endOfMonth()->endOfDay();

                    $query = Customer::query();
                    $query->join('customer_shops as cs', 'customers.id', '=', 'cs.customer_id')
                        ->where('cs.shop_id', $shop->shop_id)
                        ->whereBetween('customers.' . Customer::UPDATED_AT, [$startMonth, $endMonth])
                        ->where('customers.deleted_at', null);

                    $result[$year][$i]['data'][$month] = $query->count();
                }
            }
        }

        return $result;
    }

    public function getBlockCount(ReportRequest $reportRequest, Collection $shops): array
    {
        $period = $this->liffService->getPeriodReport($reportRequest);

        foreach (range($period['start_year'], $period['end_year']) as $year) {
            foreach ($shops as $i => $shop) {
                $result[$year][$i]['shop'] = $shop;

                foreach (range(1, 12) as $month) {
                    $startMonth = Carbon::createFromDate($year, $month)->startOfMonth()->startOfDay();
                    $endMonth = Carbon::createFromDate($year, $month)->endOfMonth()->endOfDay();

                    $query = Customer::query();
                    $query->join('customer_shops as cs', 'customers.id', '=', 'cs.customer_id')
                            ->where('cs.shop_id', $shop->shop_id)
                            ->whereBetween('customers.' . Customer::UPDATED_AT, [$startMonth, $endMonth])
                            ->where('customers.deleted_at', '<>', null);

                    $result[$year][$i]['data'][$month] = $query->count();
                }
            }
        }

        return $result;
    }

    // public function getUsedCouponCount()
    // {
    //     return ;
    // }

    public function getVisitedCount(ReportRequest $reportRequest, Collection $shops): array
    {
        $period = $this->liffService->getPeriodReport($reportRequest);

        foreach (range($period['start_year'], $period['end_year']) as $year) {
            foreach ($shops as $i => $shop) {
                $result[$year][$i]['shop'] = $shop;

                foreach (range(1, 12) as $month) {
                    $startMonth = Carbon::createFromDate($year, $month)->startOfMonth()->startOfDay();
                    $endMonth = Carbon::createFromDate($year, $month)->endOfMonth()->endOfDay();

                    $checkinQuery = CustomerShop::query();
                    $checkinQuery->leftJoin('shops', 'customer_shops.shop_id', 'shops.shop_id')
                            ->where('shops.shop_id', $shop->shop_id)
                            ->whereBetween('customer_shops.visited_at', [$startMonth, $endMonth]);

                    $result[$year][$i]['data'][$month] = $checkinQuery->count();
                }
            }
        }

        return $result;
    }

    public function getVisitRate(ReportRequest $reportRequest, Collection $shops): array
    {
        $period = $this->liffService->getPeriodReport($reportRequest);

        foreach (range($period['start_year'], $period['end_year']) as $year) {
            foreach ($shops as $i => $shop) {
                $result[$year][$i]['shop'] = $shop;

                foreach (range(1, 12) as $month) {
                    $startMonth = Carbon::createFromDate($year, $month)->startOfMonth()->startOfDay();
                    $endMonth = Carbon::createFromDate($year, $month)->endOfMonth()->endOfDay();

                    $followQuery = Customer::query();
                    $followQuery->join('customer_shops as cs', 'customers.id', '=', 'cs.customer_id')
                            ->where('cs.shop_id', $shop->shop_id)
                            ->whereBetween('customers.' . Customer::UPDATED_AT, [$startMonth, $endMonth]);

                    $followCount = $followQuery->count();

                    $checkinQuery = CustomerShop::query();
                    $checkinQuery->leftJoin('shops', 'customer_shops.shop_id', 'shops.shop_id')
                            ->where('shops.shop_id', $shop->shop_id)
                            ->whereBetween('customer_shops.visited_at', [$startMonth, $endMonth]);

                    $checkinCount = $checkinQuery->count();

                    $result[$year][$i]['data'][$month] = !empty($followCount) ? floor(($followCount / $checkinCount) * 100) : 0;
                }
            }
        }

        return $result;
    }
}
