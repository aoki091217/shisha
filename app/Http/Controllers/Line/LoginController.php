<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LineRequest;
use App\Repositories\ShopRepository;
use App\Services\LiffService;
use App\Services\LineBotService;
use Hirossyi73\UrlShorter\Model\Shorter;
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
        $lineUrl = $lineBotService->getLineUrl($request->shop_id);

        return view('line.checkin', compact('lineUrl'));
    }

    public function liff(LineRequest $request): View
    {
        $client = $this->shopRepository->find($request->getShopId());
        $loginUrl = $this->liffService->getLoginUrl();

        return view('line.liff', compact('request', 'loginUrl'));
    }

    public function saveLiff(LineRequest $request)
    {
        $this->liffService->save($request);

        return 200;
    }
}
