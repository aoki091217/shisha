<?php

namespace App\Repositories;

use App\Models\CustomerShop;
use Carbon\Carbon;
use DB;

class CustomerShopRepository
{
    private $prevYear;
    private $thisYear;

    public function __construct()
    {
        $this->thisYear = Carbon::now();
        $this->prevYear =  $this->thisYear->copy()->subYear()->year;
    }

    public function getSales()
    {
        $sales = [];
        foreach (range(1, 12) as $month) {
            $month = sprintf('%02d', $month);

            $prevAmounts = $this->getAmounts($this->prevYear, $month);
            $thisAmounts = $this->getAmounts($this->thisYear->year, $month);

            $sales['month'][] = [
                'prev' => collect($prevAmounts)->sum('amount'),
                'this' => collect($thisAmounts)->sum('amount')
            ];
        }

        $sales['year'] = [
            'prev' => $this->getAmounts($this->prevYear),
            'this' => $this->getAmounts($this->thisYear->year)
        ];

        return $sales;
    }

    private function getAmounts($year, $month = null)
    {
        $format = '%Y';
        $target = $year;
        if (!is_null($month)) {
            $format = '%Y-%m';
            $target = "{$year}-{$month}";
        }

        return DB::table('bills')
            ->selectRaw('shops.name, SUM(bills.amount) AS amount')
            ->leftJoin('shops', 'bills.shop_id', 'shops.shop_id')
            ->whereRaw("DATE_FORMAT(bills.bill_date, '{$format}') = '{$target}'")
            ->groupBy('bills.shop_id')
            ->orderByDesc('amount')
            ->get();
    }

    public function getCustomers()
    {
        $customers = [];
        foreach (range(1, 12) as $month) {
            $month = sprintf('%02d', $month);

            $prevAmounts = $this->getCustomerCounts($this->prevYear, $month);
            $thisAmounts = $this->getCustomerCounts($this->thisYear->year, $month);

            $customers['month'][] = [
                'prev' => collect($prevAmounts)->sum('count'),
                'this' => collect($thisAmounts)->sum('count')
            ];
        }

        $customers['year'] = [
            'prev' => $this->getCustomerCounts($this->prevYear),
            'this' => $this->getCustomerCounts($this->thisYear->year)
        ];

        return $customers;
    }

    private function getCustomerCounts($year, $month = null)
    {
        $format = '%Y';
        $target = $year;
        if (!is_null($month)) {
            $format = '%Y-%m';
            $target = "{$year}-{$month}";
        }

        return DB::table('customer_shops')
            ->selectRaw('shops.name, COUNT(*) AS `count`')
            ->leftJoin('shops', 'customer_shops.shop_id', 'shops.shop_id')
            ->whereRaw("DATE_FORMAT(customer_shops.visit, '{$format}') = '{$target}'")
            ->groupBy('customer_shops.shop_id')
            ->orderByDesc('count')
            ->get();
    }

    public function store($line_token, $checkin)
    {
        $customer_shop = new CustomerShop();
        $customer_shop->fill(array_merge($checkin, [
            'line_token' => $line_token
        ]))->save();
    }
}

?>
