<?php

namespace App\Repositories;

use App\Models\Customer;
use Carbon\Carbon;
use DB;

class CustomerRepository
{
    public function findById(int $id): Customer
    {
        return Customer::find($id);
    }

    public function get()
    {
        return Customer::get();
    }

    public function paginate()
    {
        return Customer::paginate(10);
    }

    public function relate()
    {
        return Customer::with('customerShops');
    }

    public function search($words)
    {
        return Customer::search($words);
    }

    public function find($line_token)
    {
        return Customer::where('line_token', $line_token)->first();
    }

    public function store($line_token)
    {
        return DB::transaction(function () use ($line_token) {
            $customer = new Customer();
            $customer->line_token = $line_token;
            $customer->step = 1;
            $customer->save();

            return $customer;
        });
    }

    public function storeStep($customer, $step)
    {
        $customer->step = $step;
        $customer->save();
    }

    public function update($customer, $fills)
    {
        foreach ($fills as $key => $value) {
            $customer->{$key} = $value;
        }

        $customer->save();
    }

    public function deleteName($customer)
    {
        $customer->name = null;
        $customer->step = 1;
        $customer->save();
    }

    public function getCustomersByCheckin()
    {
        $query = Customer::query();
        $query->select([
            'customers.*',
            'customer_shops.visited_at',
            'shops.name as shop_name'
        ])
            ->join('customer_shops', 'customer_shops.customer_id', 'customers.id')
            ->join('shops', 'customer_shops.shop_id', 'shops.shop_id')
            ->orderByDesc('customer_shops.visited_at');

        return $query;
    }
}

?>
