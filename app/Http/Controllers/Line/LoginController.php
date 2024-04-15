<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LineRequest;
use App\Models\Code;
use App\Repositories\CodeRepository;
use App\Repositories\ShopRepository;
use App\Services\CodeService;
use App\Services\LiffService;
use App\Services\LineBotService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(
        private LiffService $liffService,
        private CodeService $codeService,
        private ShopRepository $shopRepository,
        private CodeRepository $codeRepository
    ) {}

    public function checkin(Request $request)
    {
        $decode = $this->codeService->getCheckinDecode($request);

        $lineBotService = new LineBotService($request['shop_id']);
        $lineUrl = $lineBotService->getLineUrlWithMessage();

        return view('line.checkin', compact('lineUrl'));
    }

    public function liff(LineRequest $request): View
    {
        $decode = $this->codeService->getCheckinDecode($request);
        $request->session()->put('query_params', $decode);

        $hash = is_null(collect($request->all())->keys()->first()) ? '' : collect($request->all())->keys()->first();
        $code = $this->codeRepository->findByHash($hash);

        $loginUrl = $this->liffService->getLoginUrl();

        $lineBotService = new LineBotService($request->session()->get('query_params.sid'));
        $lineUrl = $lineBotService->getLineUrl();

        return view('line.liff', compact('code', 'request', 'loginUrl', 'lineUrl'));
    }

    public function saveLiff(LineRequest $request)
    {
        $this->liffService->save($request);

        return 200;
    }
}
