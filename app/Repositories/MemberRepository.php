<?php

namespace App\Repositories;

use App\Models\Member;
use App\Models\User;
use Auth;
use DB;

class MemberRepository
{
    public function get()
    {
        if (auth()->user()->role_id === 1) {
            return Member::get();
        } else {
            return Member::where('shop_id', auth()->user()->member->shop_id)->get();
        }
    }

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
            $user = new User();
            $user->fill(array_merge($request->user, [
                'name' => $request->member['name']
            ]))->save();

            $shopId = $request->member['shop_id'];
            if (auth()->user()->role_id !== 1) {
                $shopId = auth()->user()->shop->shop_id;
            }

            $member = new Member();
            $member->fill(array_merge($request->member, [
                'shop_id' => $shopId,
                'user_id' => $user->id
            ]))->save();
        });
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $member = $this->find($id);
            $member->fill($request)->save();

            return $member;
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
