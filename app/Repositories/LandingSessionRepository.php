<?php

namespace App\Repositories;

use App\Models\LandingSession;
use App\Models\Shop;

class LandingSessionRepository
{
    public function __construct(
        private LandingSession $model
    ){}

    public function findByToken(string $sessionToken): ?LandingSession
    {
        return LandingSession::where('session_token', $sessionToken)->first();
    }

    public function store(Shop $shop, array $parameters, ?string $referrer): LandingSession
    {
        return LandingSession::create([
            'shop_id' => $shop->shop_id,
            'session_token' => \Str::random(32),
            'parameters' => $parameters,
            'referrer' => $referrer,
        ]);
    }

    public function update(LandingSession $landingSession, array $attributes): LandingSession
    {
        $landingSession->fill($attributes)->save();
        return $landingSession;
    }

    public function findLatestByShopId(int $shopId): LandingSession|null
    {
        return $this->model->query()->where(LandingSession::SHOP_ID, $shopId)->orderByDesc(LandingSession::CREATED_AT)->first();
    }
}
