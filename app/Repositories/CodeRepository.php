<?php

namespace App\Repositories;

use App\Http\Requests\CodeRequest;
use App\Models\Code;
use DB;

class CodeRepository
{
    public function get()
    {
        if (auth()->user()->role_id === 1) {
            return Code::get();
        } else {
            return Code::where('shop_id', auth()->user()->member->shop_id)->get();
        }
    }

    public function findByCodeId(int $codeId): Code
    {
        return Code::find($codeId);
    }

    public function findByHash(string $hash): Code
    {
        return Code::where(Code::HASH, $hash)->firstOrNew();
    }


    public function store(CodeRequest $request): void
    {
        $codes = $request->getCodes();

        DB::transaction(function () use ($codes) {
            $model = new Code();
            $model->fill($codes)->save();
        });
    }

    public function update(CodeRequest $request, int $id): void
    {
        $code = $this->findByCodeId($id);
        $codes = $request->getCodes();

        DB::transaction(function () use ($codes, $code) {
            $code->fill($codes)->save();
        });
    }

    public function delete(int $codeId): void
    {
        $code = $this->findByCodeId($codeId);

        DB::transaction(function () use ($code) {
            $code->delete();
        });
    }
}

?>
