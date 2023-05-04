<?php

namespace App\Repositories;

use App\Models\Bland;
use App\Models\Flavor;
use DB;

class BlandRepository
{
    public function get()
    {
        return Bland::get();
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
