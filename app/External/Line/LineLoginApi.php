<?php

namespace App\External\Line;

use App\Models\External\LineUser;
use App\Models\Shop;
use GuzzleHttp\Client;

/**
 * LINEログインAPI
 * @see: https://developers.line.biz/ja/reference/line-login/
 */
class LineLoginApi
{
    public function __construct(private Client $client)
    {}

    /**
     * LIFFで取得したアクセストークンからLINEの登録情報を取得するAPI
     * @param Shop $shop
     * @param string $access_token
     * @return LineUser
     * @throws \Exception
     */
    public function verifyByAccessToken(Shop $shop, string $access_token): LineUser
    {
        $verifyResponse = $this->client->get(
            'https://api.line.me/oauth2/v2.1/verify',
            [
                'query' => ['access_token' => $access_token],
            ]
        );

        $verify = json_decode($verifyResponse->getBody()->getContents(), true);
        if (!isset($verify['client_id']) || $verify['client_id'] !== $shop->liff_channel_id) {
            \Log::error('Invalid access_token', [
                'endpoint' => '/oauth2/v2.1/verify',
                'shopId' => $shop->shop_id,
                'response' => $verifyResponse->getBody(),
                'status' => $verifyResponse->getStatusCode(),
            ]);
            throw new \Exception('Invalid token');
        }

        $userinfoResponse = $this->client->get(
            'https://api.line.me/oauth2/v2.1/userinfo',
            [
                'headers' => ['Authorization' => "Bearer {$access_token}"],
            ]
        );
        $userinfo = json_decode($userinfoResponse->getBody()->getContents(), true);
        if (!isset($userinfo['sub'])) {
            \Log::error('Invalid token', [
                'endpoint' => '/oauth2/v2.1/userinfo',
                'shopId' => $shop->shop_id,
                'response' => $userinfoResponse->getBody(),
                'status' => $userinfoResponse->getStatusCode(),
            ]);
            throw new \Exception('Invalid token');
        }

        $friendshipResponse = $this->client->get(
            'https://api.line.me/friendship/v1/status',
            [
                'headers' => ['Authorization' => "Bearer {$access_token}"],
            ]
        );
        $friendship = json_decode($friendshipResponse->getBody()->getContents(), true);
        if (!isset($friendship['friendFlag'])) {
            \Log::error('Invalid token', [
                'endpoint' => '/friendship/v1/status',
                'shopId' => $shop->shop_id,
                'response' => $friendshipResponse->getBody(),
                'status' => $friendshipResponse->getStatusCode(),
            ]);
            throw new \Exception('Invalid token');
        }

        return new LineUser($userinfo['sub'], $userinfo['name'], $userinfo['picture'] ?? null, $friendship['friendFlag'], true);
    }
}
