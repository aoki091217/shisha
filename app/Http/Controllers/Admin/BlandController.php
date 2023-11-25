<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlandRequest;
use App\Repositories\BlandRepository;
use App\Repositories\ShopRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class BlandController extends Controller
{
    public function __construct(
        private ShopRepository $shopRepository,
        private BlandRepository $blandRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $shops = $this->shopRepository->get();
        $blands = $this->blandRepository->search($request->bland)->paginate();

        return view('bland.index', compact('blands', 'shops'));
    }

    public function create()
    {
        $shops = $this->shopRepository->get();
        return view('bland.create', compact('shops'));
    }

    public function store(BlandRequest $request)
    {
        $this->blandRepository->store($request->bland);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));
        return redirect()->route('bland.index');
    }

    public function edit($id)
    {
        $shops = $this->shopRepository->get();
        $bland = $this->blandRepository->find($id);
        return view('bland.edit', compact('bland', 'shops'));
    }

    public function update(BlandRequest $request, $id)
    {
        $this->blandRepository->update($request->bland, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('bland.index');
    }

    public function destroy($id)
    {
        $this->blandRepository->delete($id);
        $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));
        return redirect()->route('bland.index');
    }
}
