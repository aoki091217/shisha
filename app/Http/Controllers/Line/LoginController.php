<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Services\CodeService;
use App\Services\LineBotService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(
        private CodeService $codeService,
    ) {}

    public function checkin(Request $request)
    {
        $decode = $this->codeService->getCheckinDecode($request);

        $lineBotService = new LineBotService($request['shop_id']);
        $lineUrl = $lineBotService->getLineUrlWithMessage();

        return view('line.checkin', compact('lineUrl'));
    }
}
