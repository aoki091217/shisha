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

    public function getCodeReport(array $filters): array
    {
        $wheres = [];
        $params = [];

        if (empty($filters['codeIds'])) {
            throw new \InvalidArgumentException('codeIds is required');
        } else {
            $wheres[] = 'codes.code_id IN (' . implode(',', array_fill(0, count($filters['codeIds']), '?')) . ')';
            array_push($params, ...$filters['codeIds']);
        }

        if (empty($filters['shopIds'])) {
            throw new \InvalidArgumentException('shopIds is required');
        } else {
            $wheres[] = 'landing_sessions.shop_id IN (' . implode(',', array_fill(0, count($filters['shopIds']), '?')) . ')';
            array_push($params, ...$filters['shopIds']);
        }

        if (!empty($filters['startDate'])) {
            $wheres[] = 'landing_sessions.created_at >= ?';
            $params[] = $filters['startDate']->startOfDay();
        }

        if (!empty($filters['endDate'])) {
            $wheres[] = 'landing_sessions.created_at <= ?';
            $params[] = $filters['endDate']->endOfDay();
        }

        $where_str = implode("\n      AND ", $wheres);

        return \DB::select(
            <<<"SQL"
            WITH A AS (
                SELECT
                    DATE(landing_sessions.created_at) AS date,
                    1 AS clicked,
                    IF(landing_sessions.conversion_status = 'mark_conversion', 1, 0) AS followed,
                    IF(customer_shop_statuses.first_visited_at IS NOT NULL, 1, 0) AS visited,
                    IF(customer_shop_statuses.friend_status = 'unfollowed', 1, 0) AS blocked
                FROM
                    landing_sessions
                JOIN
                    codes
                    ON landing_sessions.parameters->>"$.hash" = codes.hash
                LEFT JOIN
                    customer_shop_statuses
                    ON landing_sessions.customer_id = customer_shop_statuses.customer_id
                    AND landing_sessions.shop_id = customer_shop_statuses.shop_id
                    AND landing_sessions.conversion_status = 'mark_conversion'
                WHERE ${where_str}
            )
            SELECT
                date,
                SUM(clicked) AS click_count,
                SUM(followed) AS follow_count,
                SUM(visited) AS visit_count,
                SUM(visited) / SUM(followed) AS visited_rate,
                SUM(blocked) AS block_count
            FROM A
            GROUP BY date
            SQL,
            $params
        );
    }
    public function getCustomerReport(array $filters): array
    {
        $wheres = [];
        $params = [];


        if (empty($filters['shopIds'])) {
            throw new \InvalidArgumentException('shopIds is required');
        } else {
            $wheres[] = 'customer_shops.shop_id IN (' . implode(',', array_fill(0, count($filters['shopIds']), '?')) . ')';
            array_push($params, ...$filters['shopIds']);
        }

        if (!empty($filters['startDate'])) {
            $wheres[] = 'customer_shops.created_at >= ?';
            $params[] = $filters['startDate']->startOfDay();
        }

        if (!empty($filters['endDate'])) {
            $wheres[] = 'customer_shops.created_at <= ?';
            $params[] = $filters['endDate']->endOfDay();
        }

        $where_str = implode("\n      AND ", $wheres);

        return \DB::select(
            <<<"SQL"
            WITH filtered_shops AS (
                SELECT customer_id, visited_at, DATE(created_at) AS date
                FROM sin_crm.customer_shops
                WHERE ${where_str}

            )
            SELECT 
                -- 最初のSQL: 月に初めて来店した顧客数
                (SELECT COUNT(customer_id)
                 FROM filtered_shops
                ) AS first_time_visitors,
        
                -- 第二のSQL: 一度しか来店していない新規顧客数
                (SELECT COUNT(DISTINCT customer_id) AS new_unique_customers
                 FROM (
                     SELECT customer_id, MIN(visited_at) AS first_visit_date
                     FROM filtered_shops
                     GROUP BY customer_id
                     HAVING COUNT(*) = 1
                 ) AS first_visits
                ) AS new_unique_customers,
        
                -- 第三のSQL: 複数回訪れた顧客数
                (SELECT COUNT(*) AS repeater_count
                 FROM (
                     SELECT customer_id
                     FROM filtered_shops
                     GROUP BY customer_id
                     HAVING COUNT(*) > 1
                 ) AS repeaters
                ) AS repeater_count,
        
                -- 第四のSQL: 14日以内に再訪問したリピーター数
                (SELECT COUNT(DISTINCT first_visit.customer_id) AS repeater_within_14_days
                 FROM (
                     SELECT customer_id, MIN(visited_at) AS first_visit_date
                     FROM filtered_shops
                     GROUP BY customer_id
                 ) AS first_visit
                 JOIN filtered_shops AS second_visit ON first_visit.customer_id = second_visit.customer_id
                 WHERE second_visit.visited_at > first_visit.first_visit_date
                 AND second_visit.visited_at <= DATE_ADD(first_visit.first_visit_date, INTERVAL 14 DAY)
                ) AS repeater_within_14_days,
        
                -- 最後のSQL: 2月に来店したユニークな顧客数
                (SELECT COUNT(DISTINCT customer_id) AS unique_visitors_in_month
                 FROM filtered_shops
                ) AS unique_visitors_in_month,

                -- 日付フィールドを追加してエラーを回避
                (SELECT MIN(date) AS min_date
                 FROM filtered_shops
                ) AS min_date,

                (SELECT MAX(date) AS max_date
                 FROM filtered_shops
                ) AS max_date
            SQL,
            $params
        );
        
        
    }
}
