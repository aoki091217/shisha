<?php

namespace App\Repositories;

use App\Models\Shop;
use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\DB;
use Storage;

class ShopRepository
{
    public function get()
    {
        if (auth()->user()->role_id === 1) {
            return Shop::get();
        } else {
            return Shop::where('shop_id', auth()->user()->member->shop_id)->get();
        }
    }

    public function paginate()
    {
        return Shop::paginate(10);
    }

    public function relate()
    {
        return Shop::with(['blands', 'members']);
    }

    public function search($words)
    {
        return Shop::search($words);
    }

    public function find($id)
    {
        return Shop::find($id);
    }

    public function findByUserId($userId)
    {
        return Shop::where('user_id', $userId)->first();
    }

    public function getMembers($shop)
    {
        return optional($shop)->members;
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $shop = new Shop();
            $shop->fill($request->shop)->save();

            $uri = route('line.checkin', ['shop_id' => $shop->shop_id]);
            $qrCode = Builder::create()
                ->writer(new PngWriter)
                ->data($uri)
                ->encoding(new Encoding('UTF-8'))
                ->build();

            Storage::disk('public')->makeDirectory($shop->shop_id);
            $qrCode->saveToFile(storage_path("app/public/{$shop->shop_id}/qr.png"));
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
