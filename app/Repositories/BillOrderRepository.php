<?php

namespace App\Repositories;

use App\Models\BillOrder;
use DB;

class BillOrderRepository
{
    public function store($request)
    {
        $request = (object) $request;
        return DB::transaction(function () use ($request) {
            $bill_order_id = !is_null(BillOrder::first()) ? BillOrder::get()->last()->bill_order_id + 1 : 1;

            foreach ($request->orders as $i => $order) {
                $order_count = $i;
                $order_count = ++$order_count;

                $insert = [];
                foreach ($order['flavors'] as $j => $flavor_id) {
                    $insert = $this->createInsert($j, $bill_order_id, $order_count, $flavor_id);

                    $bill_order = new BillOrder();
                    $bill_order->fill($insert)->save();
                }
            }

            return $bill_order_id;
        });
    }

    public function update($request)
    {
        $request = (object) $request;
        return DB::transaction(function () use ($request) {
            BillOrder::where('bill_order_id', $request->bill_order_id)->delete();

            $bill_order_id = !is_null(BillOrder::get()->last()) ? BillOrder::get()->last()->bill_order_id + 1 : 1;
            foreach ($request->orders as $i => $order) {
                $order_count = $i;
                $order_count = ++$order_count;

                $insert = [];
                foreach ($order['flavors'] as $j => $flavor_id) {
                    $insert = $this->createInsert($j, $bill_order_id, $order_count, $flavor_id);

                    $bill_order = new BillOrder();
                    $bill_order->fill($insert)->save();
                }
            }

            return $bill_order_id;
        });
    }

    private function createInsert($j, $bill_order_id, $order_count, $flavor_id)
    {
        $flavor_count = $j;
        $flavor_count = ++$flavor_count;
        return [
            'bill_order_id' => $bill_order_id,
            'order_id' => $order_count,
            'flavor_id' => intval($flavor_id)
        ];
    }
}

?>
