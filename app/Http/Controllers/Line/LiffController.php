<?php

namespace App\Http\Controllers\Line;

use App\External\Line\LineLoginApi;
use App\Http\Controllers\Controller;
use App\Models\LandingSession;
use App\Models\Shop;
use App\Repositories\CodeRepository;
use App\Repositories\LandingSessionRepository;
use App\Repositories\ShopRepository;
use App\Services\CustomerService;
use App\Services\LandingSessionService;
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
        $session = null;
        return view('line.liff', compact('shop', 'session'));
    }

    /**
     * 広告トラッキングのランディングページ
     * @param Request $request
     * @param LandingSessionRepository $landingSessionRepository
     * @return View
     */
    public function code(
        Request $request,
        LandingSessionRepository $landingSessionRepository,
    ): View
    {
        $shop = $this->getShop($request);
        $session = $landingSessionRepository->store($shop, $request->query(), $request->header('referer'));

        return view('line.liff', compact('shop', 'session'));
    }

    /**
     * 広告トラッキングでLIFF認証後のリダイレクトページ
     * @param Request $request
     * @param LandingSessionRepository $landingSessionRepository
     * @return View
     */
    public function callback(
        Request $request,
        LandingSessionRepository $landingSessionRepository,
    ): View
    {
        $shop = $this->getShop($request);
        $sessionToken = $request->route('session_token');
        $session = $landingSessionRepository->findByToken($sessionToken);
        if (!$session || $session->shop_id !== $shop->shop_id) {
            throw new HttpResponseException(response('not found', 404));
        }

        return view('line.liff', compact('shop', 'session'));
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
        LandingSessionService $landingSessionService,
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

        // セッションを更新する
        if ($sessionToken) {
            try {
                $session = $landingSessionService->completeSession($sessionToken, $data['customer'], $data['conversion_status']);
                if ($session->conversion_status === LandingSession::CONVERSION_STATUS_MARK_CONVERSION) {
                    // コンバージョン判定の時のみサンクスページにリダイレクト
                    return response()->json([
                        'redirectUri' => route('liff.thanks', ['shop_id' => $shop->shop_id, 'session_token' => $session->session_token]),
                    ]);
                }
            } catch (\InvalidArgumentException $e) {
                // ユーザー起因で発生しうるエラーだが、念のためwarningエラーとして記録しておく
                \Log::warning($e->getMessage(), [
                    'shop_id' => $shop->shop_id,
                    'session_token' => $sessionToken,
                ]);
            }
        }

        // 非コンバージョン判定時はLINEのルームに遷移
        return response()->json([
            'redirectUri' => $shop->getRoomUri(),
        ]);
    }

    /**
     * コンバージョン発生時にコンバージョンタグを発火させるためのページ
     * このページにLIFFのタグは入っていない
     * @param Request $request
     * @param LandingSessionRepository $landingSessionRepository
     * @param CodeRepository $codeRepository
     * @return View
     */
    public function thanks(
        Request $request,
        LandingSessionRepository $landingSessionRepository,
        CodeRepository $codeRepository,
    ): View
    {
        $shop = $this->getShop($request);
        $sessionToken = $request->route('session_token');
        $session = $landingSessionRepository->findByToken($sessionToken);
        if (!$session || $session->shop_id !== $shop->shop_id || $session->conversion_status !== LandingSession::CONVERSION_STATUS_MARK_CONVERSION) {
            throw new HttpResponseException(response('not found', 404));
        }

        $script = null;
        try {
            $hash = $session->parameters['hash'] ?? null;
            if ($hash) {
                $code = $codeRepository->findByHash($hash);
                $script = $code->script;
            }
        } catch (\Throwable $e) {
            // ここのエラーはログだけ残して握りつぶす
            \Log::error($e->getMessage(), [
                'shop_id' => $shop->shop_id,
                'session_token' => $sessionToken,
            ]);
        }

        return view('line.thanks', compact('shop', 'session', 'script'));
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
