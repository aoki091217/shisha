<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Customer;
use App\Models\Message;
use App\Models\Situation;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerShopRepository;
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

            $customer = Customer::where('line_token', $line_token)->first();

            switch ($event) {
                case ($event instanceof FollowEvent):
                    $situation = Situation::with('messages.carousels.carouselActions')->where('shop_id', $shop->shop_id)->where('event_type', 1)->first();

                    if (!is_null($situation)) {
                        foreach ($situation->messages as $message) {
                            $lineBotService->push($line_token, $message);
                        }
                    }

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
                        }

                        if (!is_null($replySituation)) {
                            foreach ($replySituation->messages as $message) {
                                $lineBotService->reply($reply_token, $message, $line_token);
                            }
                        }

                        if (Answer::where('customer_id', $customer->id)->count() === 0) {
                            $question = Situation::with('messages.carousels.carouselActions')->where('shop_id', $shop->shop_id)->where('event_type', 3)->first();

                            if (!is_null($question)) {
                                foreach ($question->messages as $index => $message) {
                                    if ($message->type === 1) {
                                        $lineBotService->push($line_token, $message);

                                        if (isset($question->messages[$index + 1])) {
                                            $lineBotService->push($line_token, $question->messages[$index + 1]);
                                            return;
                                        }
                                    } else {
                                        $lineBotService->reply($reply_token, $message, $line_token);
                                        return;
                                    }
                                }
                            }
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
                                $lineBotService->buildReplyMessage($reply_token, "{$customer->name}様、ご登録ありがとうございます。");

                                // $greeting = Situation::with('messages.carousels.carouselActions')->where('shop_id', $shop->shop_id)->where('event_type', 1)->first();
                                // if (!is_null($greeting)) {
                                //     foreach ($greeting->messages as $message) {
                                //         $lineBotService->push($line_token, $message);
                                //     }
                                // }
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
                        $lineBotService->buildPushMessage($line_token, 'そのメッセージには、すでに回答済みです');
                        return;
                    }

                    $message = Message::find($postbacks['next_message_id']);

                    if (is_null($message)) {
                        $customer = Customer::where('line_token', $line_token)->first();
                        if (is_null($customer->name)) {
                            $lineBotService->buildPushMessage($line_token, 'ニックネームが未登録です。ニックネームを入力してください。');
                        }
                    } else {
                        $lineBotService->push($line_token, $message);
                        return;
                    }

                    return;
                case ($event instanceof UnfollowEvent):
                    $customer = Customer::where('line_token', $line_token)->first();
                    if (!is_null($customer)) {
                        $customer->delete();
                    }

                    return;
            }
        }

        return response('OK', 200);
    }
}
