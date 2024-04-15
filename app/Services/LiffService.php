<?php

namespace App\Services;

use App\Http\Requests\Api\LineRequest;
use App\Http\Requests\ReportRequest;
use App\Models\Liff;
use App\Models\Shop;
use Str;

class LiffService
{
    public function save(LineRequest $request): void
    {
        if (!empty($request->getLineToken()) && !empty($request->getQueryParams())) {
            $queryParam = $this->createQueryParam($request->getQueryParams());

            $liff = new Liff();
            $liff->fill([
                'shop_id' => $request->getQueryParams()['sid'],
                'line_token' => $request->getLineToken(),
                'query' => $queryParam
            ])->save();
        }
    }

    public function createQueryParam(array $queryParams): string
    {
        if (empty($queryParams)) return '';

        foreach ($queryParams as $key => $value) {
            $params[] = "{$key}={$value}";
        }

        return join('&', $params);
    }

    public function getLiffUrl($shopId = null): string
    {
        if (is_null($shopId)) {
            $shop = Shop::first();
        } else {
            $shop = Shop::find($shopId);
        }

        $parameter = "?sid={$shop->shop_id}";
        $liffUrl = 'https://liff.line.me/' . config('services.line.liff_id') . $parameter;

        return $liffUrl;
    }

    public function getLoginUrl(): string
    {
        $state = Str::random(32);
        $nonce  = Str::random(32);
        $domain = app()->isProduction() ? env('APP_URL') : env('APP_NGROK');

        $uri = 'https://access.line.me/oauth2/v2.1/authorize?';
        $response_type = 'response_type=code';
        $client_id = '&client_id='.config('services.line.client_id');
        $redirect_uri = "&redirect_uri={$domain}/api/line/save_liff";
        $state_uri = "&state={$state}";
        $scope = '&scope=openid%20profile';
        $prompt = '&prompt=consent';
        $nonce_uri = "&nonce={$nonce}";
        $bot_prompt = '&bot_prompt=aggressive';

        return $uri . $response_type . $client_id . $redirect_uri . $state_uri . $scope . $prompt . $nonce_uri . $bot_prompt;
    }

    public function getPeriodReport(ReportRequest $reportRequest): array
    {
        $period = [
            'start_year' => now()->year,
            'end_year' => now()->year
        ];
        if (isset($reportRequest[ReportRequest::START_YEAR]) && isset($reportRequest[ReportRequest::END_YEAR])) {
            $period = [
                'start_year' => $reportRequest[ReportRequest::START_YEAR],
                'end_year' => $reportRequest[ReportRequest::END_YEAR]
            ];
        }

        return $period;
    }
}

?>
