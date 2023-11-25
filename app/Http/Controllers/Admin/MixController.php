<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MixRequest;
use App\Models\Bland;
use App\Repositories\BlandRepository;
use App\Repositories\FlavorRepository;
use App\Repositories\MixRepository;
use App\Repositories\ShopRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class MixController extends Controller
{
    public function __construct(
        private MixRepository $mixRepository,
        private BlandRepository $blandRepository,
        private ShopRepository $shopRepository,
        private FlavorRepository $flavorRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $mixPresets = $this->mixRepository->relate()->search($request->mix)->paginate();

        return view('mix.index', compact('mixPresets'));
    }

    public function create(Request $request)
    {
        $shops = $this->shopRepository->get();
        $blands = $this->blandRepository->relate()->get();

        if (auth()->user()->role_id !== 1) {
            $blands = $blands->where('shop_id', auth()->user()->member->shop_id);
        }

        return view('mix.create', compact('blands', 'shops'));
    }

    public function store(MixRequest $request)
    {
        $this->mixRepository->store($request->mix);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));
        return redirect()->route('mix.index');
    }

    public function show($id)
    {
        $mixPreset = $this->mixRepository->relate()->find($id);
        return view('mix.show', compact('mixPreset'));
    }

    public function edit($id)
    {
        $mixPreset = $this->mixRepository->relate()->find($id);
        $blands = $this->blandRepository->relate()->get();
        return view('mix.edit', compact('mixPreset', 'blands'));
    }

    public function update(MixRequest $request, $id)
    {
        $this->mixRepository->update($request->mix, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('mix.index');
    }

    public function destroy($id)
    {
        $this->mixRepository->delete($id);
        $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));
        return redirect()->route('mix.index');
    }

    public function getFlavors(Request $request)
    {
        $flavors = Bland::find($request->bland_id)?->flavors;
        return response()->json($flavors);
    }
}
