<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Repositories\ShopRepository;

class ShopController extends Controller
{
    public function __construct(
        private ShopRepository $shop_repository
    ) {}

    public function index()
    {
        $shops = $this->shop_repository->paginate();
        return view('shop.index', compact('shops'));
    }

    public function store(StoreRequest $request)
    {
        $this->shop_repository->store($request->shop);
        return redirect()->route('shop.index');
    }

    public function edit($id)
    {
        $shop = $this->shop_repository->find($id);
        return view('shop.edit', compact('shop'));
    }

    public function update(StoreRequest $request, $id)
    {
        $this->shop_repository->update($request->shop, $id);
        return redirect()->route('shop.index');
    }

    public function destroy($id)
    {
        $this->shop_repository->delete($id);
        return redirect()->route('shop.index');
    }
}
