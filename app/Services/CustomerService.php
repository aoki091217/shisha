<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerShop;
use App\Models\External\LineUser;
use App\Models\Shop;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerShopRepository;

class CustomerService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private CustomerShopRepository $customerShopRepository,
    )
    {}

    /**
     * LineUserからCustomerとCustomerShopを登録/更新する
     * @param Shop $shop
     * @param LineUser $lineUser
     * @return array{customer: Customer, customer_shop: CustomerShop, isConversion: bool, markActivated: bool}
     * @throws \Throwable
     */
    public function storeByLineUser(Shop $shop, LineUser $lineUser)
    {
        $customer = $this->customerRepository->find($lineUser->line_id);
        $customer_shop = null;
        if ($customer) {
            $customer_shop = $customer->customerShops()->where('shop_id', $shop->shop_id)->first();
        }

        // Note: 未登録の顧客の場合でも、LIFF認証で友だち登録したときのWebHookでCustomerShopのレコードが既に存在する可能性がある
        $markActivated = $lineUser->is_friend && $lineUser->is_liff_active && (!$customer_shop || !$customer_shop->activated_at);
        // TODO: 条件あとで考え直す
        $isConversion = $markActivated && (!$customer_shop || !$customer_shop->activated_at || $customer_shop->created_at->clone()->addMinutes(5)->isFeature());

        [$customer, $customer_shop] = \DB::transaction(function () use ($shop, $lineUser, $customer, $customer_shop, $markActivated) {
            if (!$customer) {
                $customer = $this->customerRepository->store($lineUser->line_id);
            }

            if (!$customer_shop) {
                $customer_shop = $this->customerShopRepository->store($customer, [
                    'shop_id' => $shop->shop_id,
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
                $customer_shop = $this->customerShopRepository->update($customer_shop, $updates);
            }

            return [$customer, $customer_shop];
        });

        return compact('customer', 'customer_shop', 'isConversion', 'markActivated');
    }
}

?>
