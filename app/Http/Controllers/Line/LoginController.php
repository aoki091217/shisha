<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LineRequest;
use App\Repositories\ShopRepository;
use App\Services\LiffService;
use App\Services\LineBotService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(
        private LiffService $liffService,
        private ShopRepository $shopRepository
    ) {}

    public function checkin(Request $request)
    {
        $lineBotService = new LineBotService($request->shop_id);
        $lineUrl = $lineBotService->getLineUrlWithMessage();

        return view('line.checkin', compact('lineUrl'));
    }

    public function liff(LineRequest $request): View
    {
        if (!empty($request->getShopId())) {
            $request->session()->put('query_params', $request->all());
        }

        $loginUrl = $this->liffService->getLoginUrl();

        $lineBotService = new LineBotService($request->session()->get('params.sid'));
        $lineUrl = $lineBotService->getLineUrl();
        $queryParams = $request->session()->get('params');

        return view('line.liff', compact('request', 'loginUrl', 'lineUrl', 'queryParams'));
    }

    public function saveLiff(LineRequest $request)
    {
        $this->liffService->save($request);

        return 200;
    }
}
