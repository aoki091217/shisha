<?php

namespace App\Repositories;

use App\Models\Member;
use DB;

class MemberRepository
{
    public function relate()
    {
        return Member::with('shop');
    }

    public function paginate()
    {
        return Member::paginate(10);
    }

    public function search($words)
    {
        return Member::search($words);
    }

    public function find($id)
    {
        return Member::find($id);
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $member = new Member();
            $member->fill($request)->save();
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $member = $this->find($id);
            $member->fill($request)->save();
        });
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $member = $this->find($id);
            $member->delete();
        });
    }
}

?>
