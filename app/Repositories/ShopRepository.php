<?php

namespace App\Repositories;

use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class ShopRepository
{
    public function paginate()
    {
        return Shop::paginate(10);
    }

    public function find($id)
    {
        return Shop::find($id);
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $shop = new Shop();
            $shop->fill($request)->save();
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $shop = $this->find($id);
            $shop->fill($request)->save();
        });
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $shop = $this->find($id);
            $shop->delete();
        });
    }
}

?>
