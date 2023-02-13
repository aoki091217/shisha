<?php

namespace App\Repositories;

use App\Models\Bill;
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
            $bill_order_id = $this->billOrderRepository->store($request);

            $insert = array_merge($request, [
                'bill_order_id' => $bill_order_id
            ]);

            $bill = new Bill();
            $bill->fill($insert)->save();
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $bill_order_id = $this->billOrderRepository->update($request);

            $insert = array_merge($request, [
                'bill_order_id' => $bill_order_id
            ]);

            $bill = $this->find($id);
            $bill->fill($insert)->save();
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
