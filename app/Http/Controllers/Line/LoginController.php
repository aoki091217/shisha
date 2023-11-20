<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LineRequest;
use App\Services\LiffService;
use App\Services\LineBotService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(
        private LiffService $liffService
    ) {}

    public function checkin(Request $request)
    {
        $lineBotService = new LineBotService($request->shop_id);
        $lineUrl = $lineBotService->getLineUrl($request->shop_id);

        return view('line.checkin', compact('lineUrl'));
    }

    public function liff(LineRequest $request): View
    {
        \Log::debug($request->all());


        return view('line.liff', compact('request'));
    }

    public function saveLiff(LineRequest $request)
    {
        $this->liffService->save($request);

        return 200;
    }
}
