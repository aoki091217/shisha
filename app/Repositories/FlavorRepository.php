<?php

namespace App\Repositories;

use App\Models\Flavor;
use DB;

class FlavorRepository
{
    public function get()
    {
        if (auth()->user()->role_id === 1) {
            return Flavor::get();
        } else {
            return Flavor::where('shop_id', auth()->user()->member->shop_id)->get();
        }
    }

    public function relate()
    {
        return Flavor::with('bland');
    }

    public function paginate()
    {
        return Flavor::paginate(10);
    }

    public function search($words)
    {
        return Flavor::search($words);
    }

    public function find($id)
    {
        return Flavor::find($id);
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $names = collect($request['names'])->reject(function ($name) {
                return is_null($name);
            });

            if (auth()->user()->role_id !== 1) {
                $request = array_merge($request, ['shop_id' => auth()->user()->member->shop_id]);
            }

            foreach ($names as $name) {
                $flavor = new Flavor();
                $flavor->fill([
                    'shop_id' => $request['shop_id'],
                    'bland_id' => $request['bland_id'],
                    'name' => $name
                ])->save();
            }
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $flavor = $this->find($id);
            $flavor->fill($request)->save();
        });
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $flavor = $this->find($id);
            $flavor->delete();
        });
    }
}

?>
