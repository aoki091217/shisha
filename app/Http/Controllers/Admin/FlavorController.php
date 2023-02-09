<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FlavorRequest;
use App\Repositories\BlandRepository;
use App\Repositories\FlavorRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class FlavorController extends Controller
{
    public function __construct(
        private BlandRepository $blandRepository,
        private FlavorRepository $flavorRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $blands = $this->blandRepository->get();
        $flavors = $this->flavorRepository->relate()->search($request->flavor)->paginate();
        return view('flavor.index', compact('blands', 'flavors'));
    }

    public function create()
    {
        $blands = $this->blandRepository->get();
        return view('flavor.create', compact('blands'));
    }

    public function store(FlavorRequest $request)
    {
        $this->flavorRepository->store($request->flavor);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));
        return redirect()->route('flavor.index');
    }

    public function edit($id)
    {
        $blands = $this->blandRepository->get();
        $flavor = $this->flavorRepository->relate()->find($id);
        return view('flavor.edit', compact('blands', 'flavor'));
    }

    public function update(FlavorRequest $request, $id)
    {
        $this->flavorRepository->update($request->flavor, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('flavor.index');
    }

    public function destroy($id)
    {
        $this->flavorRepository->delete($id);
        $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));
        return redirect()->route('flavor.index');
    }
}
