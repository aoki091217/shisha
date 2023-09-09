<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Services\LineBotService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function checkin(Request $request)
    {
        $lineBotService = new LineBotService((int) $request->shop_id);

        $messages = $lineBotService->createUri($request->shop_id);

        return view('line.checkin', compact('messages'));
    }
}
