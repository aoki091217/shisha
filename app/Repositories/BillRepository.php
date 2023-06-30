<?php

namespace App\Repositories;

use App\Models\Bill;
use App\Models\BillCustomer;
use Carbon\Carbon;
use DB;

class BillRepository
{
    public function __construct(
        private BillOrderRepository $billOrderRepository
    ){}

    public function relate($relations)
    {
        return Bill::with($relations);
    }

    public function paginate()
    {
        return Bill::paginate(10);
    }

    public function search($words)
    {
        return Bill::search($words);
    }

    public function find($id)
    {
        return Bill::find($id);
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $bill = new Bill();
            $bill->fill($request)->save();

            $bill->billCustomers()->createMany($request['customers']);

            $bill->billOrders()->createMany($request['mixes']);
        });
    }

    public function draft($request)
    {
        DB::transaction(function () use ($request) {
            $bill = new Bill();
            $bill->fill($request)->save();

            if (isset($request['customers'])) {
                $bill->billCustomers()->createMany($request['customers']);
            }

            if (!empty($request['mixes'])) {
                $bill->billOrders()->createMany($request['mixes']);
            }
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $bill = $this->find($id);
            $bill->fill($request)->save();

            if (isset($request['customers'])) {
                if ($bill->billCustomers->isNotEmpty()) {
                    foreach ($request['customers'] as $customer) {
                        $bill->billCustomers()->update($customer);
                    }
                } else {
                    $bill->billCustomers()->createMany($request['customers']);
                }
            }

            if (!empty($request['mixes'])) {
                if ($bill->billOrders->isNotEmpty()) {
                    foreach ($request['mixes'] as $mix) {
                        $bill->billOrders()->update($mix);
                    }
                } else {
                    $bill->billOrders()->createMany($request['mixes']);
                }
            }
        });
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $bill = $this->find($id);
            $bill->delete();
        });
    }
}

?>
