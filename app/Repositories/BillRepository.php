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
            if (auth()->user()->role_id !== 1) {
                $request = array_merge($request, ['shop_id' => auth()->user()->member->shop_id]);
            }

            $bill = new Bill();
            $bill->fill($request)->save();

            $bill->billCustomers()->createMany($request['customers']);

            $bill->billOrders()->createMany($request['mixes']);
        });
    }

    public function draft($request)
    {
        DB::transaction(function () use ($request) {
            if (auth()->user()->role_id !== 1) {
                $request = array_merge($request, ['shop_id' => auth()->user()->member->shop_id]);
            }

            if (isset($request['bill_id'])) {
                $bill = $this->find($request['bill_id']);
            } else {
                $bill = new Bill();
            }
            $bill->fill($request)->save();

            $bill->billCustomers()->delete();
            if (isset($request['customers'])) {
                $bill->billCustomers()->createMany($request['customers']);
            }

            $bill->billOrders()->delete();
            if (!empty($request['mixes'])) {
                $bill->billOrders()->createMany($request['mixes']);
            }
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            if (auth()->user()->role_id !== 1) {
                $request = array_merge($request, ['shop_id' => auth()->user()->member->shop_id]);
            }

            /** @var Bill $bill */
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
