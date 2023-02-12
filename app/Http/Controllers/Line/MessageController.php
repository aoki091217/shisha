<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Repositories\CustomerShopRepository;
use App\Services\LineBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class MessageController extends Controller
{
    public function __construct(
        private Customer $customer,
        private LineBotService $lineBotService,
        private CustomerShopRepository $customerShopRepository
    ){}

    public function webhook(Request $request)
    {
        $client = new CurlHTTPClient(config('services.line.access_token'));
        $bot = new LINEBot($client, ['channelSecret' => config('services.line.channel_secret')]);

        $events = $bot->parseEventRequest($request->getContent(), $request->header('x-line-signature'));

        foreach ($events as $event) {
            $line_token = $event->getUserId();
            $reply_token = $event->getReplyToken();

            switch ($event) {
                case ($event instanceof FollowEvent):
                    $customer = $this->customer->findCustomer($line_token);
                    if (is_null($customer)) {
                        $customer = $this->customer->storeCustomer($line_token);
                        $message = 'ニックネームが未登録です。ニックネームを入力してください。';
                        $this->lineBotService->buildPushMessage($line_token, $message);
                    }

                    return;
                case ($event instanceof TextMessage):
                    $text = $event->getText();
                    if (preg_match('/checkin/', $text)) {
                        $checkin = $this->lineBotService->getParamsFromCheckin($text);
                        $this->customerShopRepository->store($line_token, $checkin);

                        $message = 'チェックインが完了いたしました。ご来店ありがとうございます。';
                        $this->lineBotService->buildReplyMessage($reply_token, $message);
                    }

                    $customer = $this->customer->findCustomer($line_token);
                    if (is_null($customer)) {
                        $customer = $this->customer->storeCustomer($line_token);
                        $message = 'ニックネームが未登録です。ニックネームを入力してください。';
                        $this->lineBotService->buildPushMessage($line_token, $message);
                    }

                    if (!preg_match('/checkin/', $text) && $customer->step === 1) {
                        $fills = [
                            'name' => $text,
                            'step' => 2
                        ];

                        $this->customer->updateCustomer($customer, $fills);
                        $this->lineBotService->buildConfirm($event, $reply_token);
                        return;
                    }

                    if ($customer->step === 2) {
                        if ($text === 'はい') {
                            $this->customer->storeStep($customer, 3);
                            $message = "{$customer->name}様、ご登録ありがとうございます。";
                            $this->lineBotService->buildReplyMessage($reply_token, $message);

                        } elseif ($text === 'いいえ') {
                            $this->customer->deleteName($customer);
                            $message = '他のニックネームをご入力ください。';
                            $this->lineBotService->buildReplyMessage($reply_token, $message);
                            return;
                        }
                    }

                    if ($customer->step === 3) {
                        $this->customer->storeStep($customer, 4);

                        $question = 'お手数おかけしますが、アンケートのご協力をお願いいたします。';
                        $this->lineBotService->buildPushMessage($line_token, $question);

                        $this->lineBotService->buildSex($line_token);
                    }

                    $replies = (object) [
                        'sex' => $this->lineBotService->getIsReplied('sex', $text),
                        'generation' => $this->lineBotService->getIsReplied('generation', $text),
                        'reason' => $this->lineBotService->getIsReplied('reason', $text)
                    ];

                    if ($replies->sex && is_null($customer->sex)) {
                        $fills = [
                            'sex' => $this->lineBotService->getQuestionValue('sex', $text)
                        ];

                        $this->customer->updateCustomer($customer, $fills);

                        $this->lineBotService->buildGeneration($line_token);
                    }

                    if ($replies->generation && is_null($customer->generation)) {
                        $fills = [
                            'generation' => $this->lineBotService->getQuestionValue('generation', $text)
                        ];

                        $this->customer->updateCustomer($customer, $fills);

                        $this->lineBotService->buildReason($line_token);
                    }

                    // 来店経路の選択の保存
                    if ($replies->reason && is_null($customer->reason)) {
                        $fills = [
                            'reason' => $text
                        ];

                        $this->customer->storeStep($customer, 5);

                        $this->customer->updateCustomer($customer, $fills);

                        $question = 'アンケートへのご協力ありがとうございました。';
                        $this->lineBotService->buildPushMessage($line_token, $question);
                    }

                // case ($event instanceof UnfollowEvent):
                //     $this->customer->deleteCustomer($line_token);
                //     return;
            }
        }

        return '200';
    }
}
