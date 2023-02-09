<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopRequest;
use App\Repositories\ShopRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        private ShopRepository $shopRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $shops = $this->shopRepository->search($request->shop)->paginate();
        return view('shop.index', compact('shops'));
    }

    public function create()
    {
        return view('shop.create');
    }

    public function store(ShopRequest $request)
    {
        $this->shopRepository->store($request->shop);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));
        return redirect()->route('shop.index');
    }

    public function edit($id)
    {
        $shop = $this->shopRepository->find($id);
        return view('shop.edit', compact('shop'));
    }

    public function update(ShopRequest $request, $id)
    {
        $this->shopRepository->update($request->shop, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('shop.index');
    }

    public function destroy($id)
    {
        $this->shopRepository->delete($id);
        $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));
        return redirect()->route('shop.index');
    }
}
