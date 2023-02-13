<?php

namespace App\Repositories;

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
        return User::find($id);
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $user = new User();
            $user->fill($request)->save();
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $user = $this->find($id);
            $user->fill($request)->save();
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
