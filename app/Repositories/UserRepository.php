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

    public function find($code)
    {
        return User::where('code', $code)->first();
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $user = new User();
            $user->fill($request)->save();
        });
    }

    public function update($request, $code)
    {
        DB::transaction(function () use ($request, $code) {
            DB::table('users')->where('code', $code)->update($request);
        });
    }

    public function delete($code)
    {
        DB::transaction(function () use ($code) {
            DB::table('users')->where('code', $code)->delete();
        });
    }
}

?>
