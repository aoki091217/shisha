<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Services\LineBotService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(
        private LineBotService $lineBotService
    ){}

    public function checkin(Request $request)
    {
        $messages = $this->lineBotService->createUri($request->shop_id);
        return view('line.checkin', compact('messages'));
    }
}
