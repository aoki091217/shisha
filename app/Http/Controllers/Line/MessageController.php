<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Customer;
use App\Models\CustomerShopStatus;
use App\Models\Message;
use App\Models\Shop;
use App\Models\Situation;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerShopRepository;
use App\Repositories\CustomerShopStatusRepository;
use App\Repositories\ShopRepository;
use App\Services\LineBotService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class MessageController extends Controller
{
    public function __construct(
        private ShopRepository $shopRepository,
        private CustomerRepository $customerRepository,
        private CustomerShopRepository $customerShopRepository,
        private CustomerShopStatusRepository $customerShopStatusRepository,
        private MessageService $messageService
    ) {}

    public function webhook(Request $request, $id)
    {
        $lineBotService = new LineBotService($id);

        $shop = $this->shopRepository->find($id);

        $client = new CurlHTTPClient($shop->line_token);
        $bot = new LINEBot($client, ['channelSecret' => $shop->channel_secret]);

        $events = $bot->parseEventRequest($request->getContent(), $request->header('x-line-signature'));

        foreach ($events as $event) {
            $line_token = $event->getUserId();
            $reply_token = $event->getReplyToken();

            /** @var Customer $customer */
            $customer = Customer::withTrashed()->where('line_token', $line_token)->first();

            switch ($event) {
                case ($event instanceof FollowEvent):
                    $situation = Situation::with('messages.carousels.carouselActions')->where('shop_id', $shop->shop_id)->where('event_type', 1)->first();

                    if (!is_null($situation)) {
                        foreach ($situation->messages as $message) {
                            $lineBotService->reply($reply_token, $message, $line_token);
                        }
                    }

                    if (!is_null($customer) && $customer->isDeleted()) {
                        $customer->restore();
                    }

                    if (is_null($customer)) {
                        $customer = $this->customerRepository->store($line_token);
                    }

                    $customer_shop_status = $this->firstOrCreateCustomerShopStatus($shop, $customer);

                    return;
                case ($event instanceof TextMessage):
                    if (is_null($customer)) {
                        $customer = $this->customerRepository->store($line_token);
                    }

                    /** @var TextMessage $event */
                    $text = $event->getText();

                    $replySituation = Situation::with('messages.carousels.carouselActions')->where('shop_id', $shop->shop_id)->where('event_type', 2)->first();

                    if (preg_match('/checkin/', $text)) {
                        $params = $lineBotService->getQueryParams($text);

                        if ($lineBotService->checkDuplicate($params, $customer)) {
                            $checkin = $lineBotService->getParamsFromCheckin($params);
                            $this->customerShopRepository->store($customer, $checkin);

                            $customer_shop_status = $this->firstOrCreateCustomerShopStatus($shop, $customer);
                            $customer_shop_status = $this->customerShopStatusRepository->checkin($customer_shop_status);
                        }

                        if (is_null($customer->name)) {
                            $lineBotService->buildReplyMessage($reply_token, 'ニックネームが未登録です。ニックネームを入力してください。');
                            return;
                        } else {
                            $lineBotService->buildReplyMessage($reply_token, '本日もご来店ありがとうございます、ごゆっくりお過ごしください。');
                            return;
                        }
                        return;
                    } else {
                        if ($customer->step === 1) {
                            $fills = [
                                'name' => $text,
                                'step' => 2
                            ];

                            $this->customerRepository->update($customer, $fills);
                            $lineBotService->buildConfirm("ニックネームは「{$event->getText()}」でよろしいですか？", $reply_token);

                            return;
                        } elseif ($customer->step === 2) {
                            if ($text === 'はい') {
                                $this->customerRepository->storeStep($customer, 3);

                                if (Answer::where('customer_id', $customer->id)->count() === 0) {
                                    $question = Situation::with('messages.carousels.carouselActions')->where('shop_id', $shop->shop_id)->where('event_type', 3)->first();
                                    $lineBotService->reply($reply_token, $question->messages->first(), $line_token);
                                }

                                return;
                            } elseif ($text === 'いいえ') {
                                $this->customerRepository->deleteName($customer);
                                $lineBotService->buildReplyMessage($reply_token, '他のニックネームをご入力ください。');

                                return;
                            }
                        }

                        if (!is_null($replySituation)) {
                            foreach ($replySituation->messages->where('keyword', $text) as $message) {
                                $lineBotService->reply($reply_token, $message, $line_token);
                            }
                        }

                        return;
                    }

                    return;
                case ($event instanceof PostbackEvent):
                    /** @var PostbackEvent $event */
                    $postback = $event->getPostbackData();

                    $postbacks = [];
                    foreach (explode('&', $postback) as $value) {
                        $exploded = explode('=', $value);
                        $postbacks[$exploded[0]] = $exploded[1];
                    }

                    $alreadyAnswer = Answer::where('carousel_id', $postbacks['carousel_id'])
                        ->where('customer_id', $postbacks['customer_id'])
                        ->first();

                    if (is_null($alreadyAnswer)) {
                        $answer = new Answer();
                        $answer->fill($postbacks)->save();
                    } else {
                        $lineBotService->buildReplyMessage($reply_token, 'そのメッセージには、すでに回答済みです');
                        return;
                    }

                    $message = Message::find($postbacks['next_message_id']);
                    $lineBotService->reply($reply_token, $message, $line_token);

                    return;
                case ($event instanceof UnfollowEvent):
                    $customer = Customer::where('line_token', $line_token)->first();
                    if (!is_null($customer)) {
                        $customer->delete();
                    }

                    $customer_shop_status = CustomerShopStatus::where('shop_id', $shop->shop_id)->where('customer_id', $customer->id)->first();
                    $customer_shop_status = $this->customerShopStatusRepository->update($customer_shop_status, [
                        'friend_status' => CustomerShopStatus::FRIEND_STATUS_UNFOLLOWED,
                    ]);

                    return;
            }
        }

        return response('OK', 200);
    }

    private function firstOrCreateCustomerShopStatus(Shop $shop, Customer $customer): CustomerShopStatus
    {
        $customer_shop_status = CustomerShopStatus::where('shop_id', $shop->shop_id)->where('customer_id', $customer->id)->first();
        if ($customer_shop_status) {
            $customer_shop_status = $this->customerShopStatusRepository->update($customer_shop_status, [
                'friend_status' => CustomerShopStatus::FRIEND_STATUS_FOLLOWED,
            ]);
        } else {
            $customer_shop_status = $this->customerShopStatusRepository->store($shop, $customer, [
                'friend_status' => CustomerShopStatus::FRIEND_STATUS_FOLLOWED,
                'liff_status' => CustomerShopStatus::LIFF_STATUS_UNKNOWN,
            ]);
        }

        return $customer_shop_status;
    }
}
