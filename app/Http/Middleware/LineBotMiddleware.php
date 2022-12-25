<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LineBotMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $this->validateSignature($request);

        return $next($request);
    }

    /**
     * LINEの署名確認
     *
     * @param Request
     * @return void
     * @throws HttpException
    */
    public function validateSignature(Request $request) : void
    {
        dd($request);
        $signature = $request->header('x-line-signature');
        if ($signature === null) {
            abort(400);
        }

        $hash = hash_hmac('sha256', $request->getContent(), config('services.line.channel_secret'), true);
        $expect_signature = base64_encode($hash);

        if (!hash_equals($expect_signature, $signature)) {
            abort(400);
        }
    }
}
