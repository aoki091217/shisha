<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MixRequest;
use App\Models\Bland;
use App\Repositories\BlandRepository;
use App\Repositories\FlavorRepository;
use App\Repositories\MixRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class MixController extends Controller
{
    public function __construct(
        private MixRepository $mixRepository,
        private BlandRepository $blandRepository,
        private FlavorRepository $flavorRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $blands = $this->blandRepository->get();
        $flavors = $this->flavorRepository->get();
        $mixPresets = $this->mixRepository->relate()->search($request->mix)->paginate();
        return view('mix.index', compact('mixPresets', 'blands', 'flavors'));
    }

    public function create(Request $request)
    {
        $blands = $this->blandRepository->relate()->get();
        return view('mix.create', compact('blands'));
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
