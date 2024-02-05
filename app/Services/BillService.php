<?php

namespace App\Services;

use App\Http\Requests\BillRequest;

class BillService
{
    public function splitBillByCustomers(BillRequest $request): array
    {
        // dd($request->bill);

        return [];
    }

    private function calcAmount(int $amount): int
    {
        return 0;
    }
}

?>
