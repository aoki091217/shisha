<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerShop;
use App\Models\CustomerShopStatus;
use App\Models\External\LineUser;
use App\Models\LandingSession;
use App\Models\Shop;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerShopStatusRepository;

class CustomerService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private CustomerShopStatusRepository $customerShopStatusRepository,
    )
    {}

    /**
     * LineUserからCustomerとCustomerShopを登録/更新する
     * @param Shop $shop
     * @param LineUser $lineUser
     * @return array{customer: Customer, customer_shop_status: CustomerShopStatus, conversion_status: string}
     * @throws \Throwable
     */
    public function storeByLineUser(Shop $shop, LineUser $lineUser)
    {
        $customer = $this->customerRepository->find($lineUser->line_id);
        $customer_shop_status = null;
        if ($customer) {
            $customer_shop_status = $customer->customerShopStatuses()->where('shop_id', $shop->shop_id)->first();
        }

        // Note: 未登録の顧客の場合でも、LIFF認証で友だち登録したときのWebHookでCustomerShopのレコードが既に存在する可能性がある
        $markActivated = $lineUser->is_friend && $lineUser->is_liff_active && (!$customer_shop_status || !$customer_shop_status->activated_at);
        if (!$lineUser->is_friend) {
            $conversion_status = LandingSession::CONVERSION_STATUS_INVALID_FRIEND_STATUS;
        } elseif ($customer_shop_status && $customer_shop_status->activated_at) {
            $conversion_status = LandingSession::CONVERSION_STATUS_ALREADY_ACTIVATED;
        } elseif ($customer_shop_status && $customer_shop_status->created_at < now()->subMinutes(1)) {
            // 1分以内に友だち登録していない場合も、セッション開始前から友だちだったとみなしてコンバージョン対象外とする
            $conversion_status = LandingSession::CONVERSION_STATUS_ALREADY_ACTIVATED;
        } else {
            // ここまでくればコンバージョン判定とする
            $conversion_status = LandingSession::CONVERSION_STATUS_MARK_CONVERSION;
        }

        [$customer, $customer_shop_status] = \DB::transaction(function () use ($shop, $lineUser, $customer, $customer_shop_status, $markActivated) {
            if (!$customer) {
                $customer = $this->customerRepository->store($lineUser->line_id);
            }

            if (!$customer_shop_status) {
                $customer_shop_status = $this->customerShopStatusRepository->store($shop, $customer, [
                    'friend_status' => $lineUser->getFriendStatus(),
                    'liff_status' => $lineUser->getLiffStatus(),
                    'activated_at' => $markActivated ? now() : null,
                ]);
            } else {
                $updates = [
                    'friend_status' => $lineUser->getFriendStatus(),
                    'liff_status' => $lineUser->getLiffStatus(),
                ];
                if ($markActivated) {
                    $updates['activated_at'] = now();
                }
                $customer_shop_status = $this->customerShopStatusRepository->update($customer_shop_status, $updates);
            }

            return [$customer, $customer_shop_status];
        });

        return compact('customer', 'customer_shop_status', 'conversion_status');
    }
}

?>
