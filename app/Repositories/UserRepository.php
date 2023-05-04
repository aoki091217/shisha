<?php

namespace App\Repositories;

use App\Models\Shop;
use App\Models\User;
use DB;

class UserRepository
{
    public function paginate()
    {
        return User::paginate(10);
    }

    public function search($words)
    {
        return User::search($words);
    }

    public function find($id)
    {
        if (is_numeric($id)) {
            return User::find($id);
        } else {
            return User::where('code', $id)->first();
        }

    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $user = new User();
            $user->fill($request)->save();

            // if (!is_null($request['shop_id'])) {
            //     $shop = new Shop();
            //     $shop->fill(array_merge($request, ['user_id' => $user->id]))->save();
            // }
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $user = $this->find($id);
            $user->fill($request)->save();

            // if (!is_null($request['shop_id'])) {
            //     $shop = Shop::find($request['shop_id']);
            //     $shop->fill(array_merge($request))->save();
            // }
        });
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $user = $this->find($id);
            $user->delete();
        });
    }
}

?>
