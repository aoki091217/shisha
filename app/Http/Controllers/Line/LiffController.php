<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Repositories\ShopRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @return Response
     */
    public function verify(Request $request): Response
    {
        // TODO
        return response('ISE', 500);
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
