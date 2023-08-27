<?php

namespace App\Http\Middleware;

use App\Repositories\ShopRepository;
use App\Services\RouteService;
use Closure;
use Illuminate\Http\Request;

class LineBotMiddleware
{
    public function __construct(
        private ShopRepository $shopRepository,
        private RouteService $routeService
    ) {}

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
        $signature = $request->header('x-line-signature');

        if ($signature === null) {
            abort(400);
        }

        $shopId = $this->routeService->getShopIdFromUrl();
        $shop = $this->shopRepository->find($shopId);

        if (is_null($shop)) {
            abort(400);
        }

        $hash = hash_hmac('sha256', $request->getContent(), $shop->channel_secret, true);
        $expect_signature = base64_encode($hash);

        if (!hash_equals($expect_signature, $signature)) {
            abort(400);
        }
    }
}
