<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CodeRequest;
use App\Repositories\CodeRepository;
use App\Repositories\ShopRepository;
use App\Repositories\SituationRepository;
use App\Services\CodeService;
use App\Services\SessionService;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    public function __construct(
        private ShopRepository $shopRepository,
        private SituationRepository $situationRepository,
        private CodeRepository $codeRepository,
        private CodeService $codeService,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $shops = $this->shopRepository->get();
        $codes = $this->codeService->get($request->all());

        return view('code.index', compact('shops', 'codes'));
    }


    public function create()
    {
        $shops = $this->shopRepository->get();
        $situations = $this->situationRepository->getByFollowEvent();

        return view('code.create', compact('shops', 'situations'));
    }


    public function store(CodeRequest $request)
    {
        $this->codeRepository->store($request);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));

        return redirect()->route('code.index');
    }


    public function show(int $codeId)
    {
        $code = $this->codeRepository->findByCodeId($codeId);

        return view('code.show', compact('code'));
    }


    public function edit(int $codeId)
    {
        $shops = $this->shopRepository->get();
        $situations = $this->situationRepository->getByFollowEvent();
        $code = $this->codeRepository->findByCodeId($codeId);

        return view('code.edit', compact('code', 'shops', 'situations'));
    }


    public function update(CodeRequest $request, int $codeId)
    {
        $this->codeRepository->update($request, $codeId);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));

        return redirect()->route('code.index');
    }


    public function destroy(int $codeId)
    {
        $this->codeRepository->delete($codeId);
        $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));

        return redirect()->route('code.index');
    }
}
