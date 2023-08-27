<?php

namespace App\Repositories;

use App\Models\Bland;
use App\Models\Flavor;
use DB;

class BlandRepository
{
    public function get()
    {
        if (auth()->user()->role_id === 1) {
            return Bland::get();
        } else {
            return Bland::where('shop_id', auth()->user()->member->shop_id)->get();
        }
    }

    public function paginate()
    {
        return Bland::paginate(10);
    }

    public function search($words)
    {
        return Bland::search($words);
    }

    public function relate()
    {
        return Bland::with('flavors');
    }

    public function find($id)
    {
        return Bland::find($id);
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            if (auth()->user()->role_id !== 1) {
                $request = array_merge($request, ['shop_id' => auth()->user()->member->shop_id]);
            }

            $bland = new Bland();
            $bland->fill($request)->save();
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $bland = $this->find($id);
            $bland->fill($request)->save();
        });
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $bland = $this->find($id);
            $bland->delete();
        });
    }
}

?>
