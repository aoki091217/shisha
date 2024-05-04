<?php

namespace App\Http\Controllers\Line;

use App\External\Line\LineLoginApi;
use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Repositories\ShopRepository;
use App\Services\CustomerService;
use App\Services\LineBotService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiffController extends Controller
{
    public function __construct(
        private ShopRepository $shopRepository,
    ) {}

    /**
     * LIFFに登録するエンドポイントURL
     * LIFFからのコールバックは一度ここを経由した後にRedirectUriにリダイレクトされるっぽい
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $shop = $this->getShop($request);
        return view('line.liff.index', compact('shop'));
    }

    /**
     * accessTokenでLINE認証を行うAPI
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(
        Request $request,
        LineLoginApi $loginApi,
        CustomerService $customerService,
    ): JsonResponse
    {
        $shop = $this->getShop($request);
        $accessToken = $request->input('accessToken');
        $sessionToken = $request->input('sessionToken');

        if (!$accessToken) {
            return response()->json(['error' => 'token is required'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $lineUser = $loginApi->verifyByAccessToken($shop, $accessToken);
        } catch (\Exception $e) {
            \Log::error('LINE verify error', [
                'shop_id' => $shop->shop_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'invalid token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = $customerService->storeByLineUser($shop, $lineUser);

        $lineBotService = new LineBotService($shop->shop_id);
        $redirectUri = $lineBotService->getLineUrl();

        // TODO: セッション周りの処理

        return response()->json([
            'redirectUri' => $redirectUri,
        ]);
    }

    /**
     * リクエストから店舗情報を取得する
     * LIFFを利用可能な店舗かのチェックもしている
     * @param Request $request
     * @return Shop
     * @throws HttpResponseException
     */
    private function getShop(Request $request): Shop
    {
        $shop_id = filter_var($request->route('shop_id'), FILTER_VALIDATE_INT);
        if (!$shop_id) {
            throw new HttpResponseException(response('not found', 404));
        }
        $shop = $this->shopRepository->find($shop_id);
        if (!$shop) {
            throw new HttpResponseException(response('not found', 404));
        }

        // 店舗にliff_idとliff_channel_idが登録されていないと機能が使えないので内部エラーにする
        if (!$shop->liff_id) {
            throw new \Exception("liff_channel_id is not set. shop_id={$shop->shop_id}");
        }

        if (!$shop->liff_channel_id) {
            throw new \Exception("liff_channel_id is not set. shop_id={$shop->shop_id}");
        }

        return $shop;
    }
}
