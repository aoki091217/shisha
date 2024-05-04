<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LandingSession;
use App\Repositories\LandingSessionRepository;

class LandingSessionService
{
    public function __construct(
        private LandingSessionRepository $landingSessionRepository
    )
    {}

    /**
     * コンバージョン状態を記録してセッションをexpireする
     * @param string $sessionToken
     * @param Customer $customer
     * @param string $conversion_status
     * @return LandingSession
     */
    public function completeSession(
        string $sessionToken,
        Customer $customer,
        string $conversion_status,
    ): LandingSession
    {
        $session = $this->landingSessionRepository->findByToken($sessionToken);
        if (!$session) {
            throw new \InvalidArgumentException('Session is not found');
        }
        if ($session->expired_at) {
            throw new \InvalidArgumentException('Session is expired');
        }

        $attributes = [
            'customer_id' => $customer->id,
            'conversion_status' => $conversion_status,
            'expired_at' => now(),
        ];
        return $this->landingSessionRepository->update($session, $attributes);
    }
}
