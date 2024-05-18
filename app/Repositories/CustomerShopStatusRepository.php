<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\CustomerShopStatus;
use App\Models\Shop;

class CustomerShopStatusRepository
{
    public function store(Shop $shop, Customer $customer, array $attributes): CustomerShopStatus
    {
        $customer_shop_status = new CustomerShopStatus();
        $customer_shop_status->fill(array_merge($attributes, [
            'shop_id' => $shop->shop_id,
            'customer_id' => $customer->id,
        ]))->save();

        return $customer_shop_status;
    }

    public function update(CustomerShopStatus $customer_shop_status, array $attributes): CustomerShopStatus
    {
        $customer_shop_status->fill($attributes)->save();
        return $customer_shop_status;
    }

    public function checkin(CustomerShopStatus $customer_shop_status): CustomerShopStatus
    {
        $customer_shop_status->fill([
            'first_visited_at' => $customer_shop_status->first_visited_at ?? now(),
            'recently_visited_at' => now(),
        ])->save();
        return $customer_shop_status;
    }
}
