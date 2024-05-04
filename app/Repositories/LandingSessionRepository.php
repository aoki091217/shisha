<?php

namespace App\Repositories;

use App\Models\LandingSession;
use App\Models\Shop;

class LandingSessionRepository
{
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
}
