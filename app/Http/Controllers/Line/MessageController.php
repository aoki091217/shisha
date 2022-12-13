<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Models\Customer;
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
    private $customer;
    private $line_service;

    public function __construct()
    {
        $this->customer = new Customer();
        $this->line_service = new LineBotService();
    }

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
                        $this->customer->storeCustomer($line_token);
                        $message = 'ニックネームが未登録です。ニックネームを入力してください。';
                        $this->line_service->buildReplyMessage($reply_token, $message);
                    } elseif ($customer->trashed()) {
                        $customer->restore();
                    }

                    return;
                case ($event instanceof TextMessage):
                    $customer = $this->customer->findCustomer($line_token);

                    $text = $event->getText();
                    if ($customer->step === 1) {
                        $fills = [
                            'name' => $text,
                            'step' => 2
                        ];

                        $this->customer->updateCustomer($customer, $fills);
                        $this->line_service->buildConfirm($event, $reply_token);
                        return;
                    }

                    if ($customer->step === 2) {
                        if ($text === 'はい') {
                            $this->customer->storeStep($customer, 3);
                            $message = "{$customer->name}様、ご登録ありがとうございます。";
                            $this->line_service->buildReplyMessage($reply_token, $message);

                        } elseif ($text === 'いいえ') {
                            $this->customer->deleteName($customer);
                            $message = '他のニックネームをご入力ください。';
                            $this->line_service->buildReplyMessage($reply_token, $message);
                            return;
                        }
                    }

                    if ($customer->step === 3) {
                        $this->customer->storeStep($customer, 4);

                        $question = 'お手数おかけしますが、アンケートのご協力をお願いいたします。';
                        $this->line_service->buildPushMessage($line_token, $question);

                        $this->line_service->buildSex($line_token);
                    }

                    $replies = (object) [
                        'sex' => $this->line_service->getIsReplied('sex', $text),
                        'generation' => $this->line_service->getIsReplied('generation', $text),
                        'reason' => $this->line_service->getIsReplied('reason', $text)
                    ];

                    if ($replies->sex && is_null($customer->sex)) {
                        $fills = [
                            'sex' => $this->line_service->getQuestionValue('sex', $text)
                        ];

                        $this->customer->updateCustomer($customer, $fills);

                        $this->line_service->buildGeneration($line_token);
                    }

                    if ($replies->generation && is_null($customer->generation)) {
                        $fills = [
                            'generation' => $this->line_service->getQuestionValue('generation', $text)
                        ];

                        $this->customer->updateCustomer($customer, $fills);

                        $this->line_service->buildReason($line_token);
                    }

                    // 来店経路の選択の保存
                    if ($replies->reason && is_null($customer->reason)) {
                        $fills = [
                            'reason' => $text
                        ];

                        $this->customer->storeStep($customer, 5);

                        if ($text === 'その他') {
                            $question = 'ご自由に来店経路を入力ください。';
                            $this->line_service->buildReplyMessage($reply_token, $question);
                        }
                    }

                    if (!$replies->reason && $customer->step === 5) {
                        $this->customer->updateCustomer($customer, $fills);

                        $question = 'アンケートへのご協力ありがとうございました。';
                        $this->line_service->buildPushMessage($line_token, $question);
                    }

                case ($event instanceof UnfollowEvent):
                    $this->customer->deleteCustomer($line_token);
                    return;
            }
        }

        return '200';
    }
}
