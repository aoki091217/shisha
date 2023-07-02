<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Customer;
use App\Models\Message;
use App\Models\SendMessage;
use App\Models\Shop;
use App\Models\Situation;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerShopRepository;
use App\Services\LineBotService;
use App\Services\MessageService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class MessageController extends Controller
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private LineBotService $lineBotService,
        private CustomerShopRepository $customerShopRepository,
        private MessageService $messageService
    ){}

    public function webhook(Request $request)
    {
        $client = new CurlHTTPClient(config('services.line.access_token'));
        $bot = new LINEBot($client, ['channelSecret' => config('services.line.channel_secret')]);

        $events = $bot->parseEventRequest($request->getContent(), $request->header('x-line-signature'));

        foreach ($events as $event) {
            $line_token = $event->getUserId();
            $reply_token = $event->getReplyToken();

            $customer = Customer::withTrashed()->where('line_token', $line_token)->first();

            switch ($event) {
                case ($event instanceof FollowEvent):
                    $name = $this->lineBotService->getProfileName($line_token);
                    if (is_null($customer)) {
                        $customer = new Customer();
                        $customer->fill([
                            'line_token' => $line_token,
                            'name' => $name
                        ])->save();
                    } else {
                        $customer->restore();
                    }

                    $situation = Situation::with('messages.carousels.carouselActions')->where('event_type', 1)->first();
                    foreach ($situation->messages as $message) {
                        $this->lineBotService->push($line_token, $message);
                    }

                    return;
                case ($event instanceof TextMessage):
                    /** @var TextMessage $event */
                    $text = $event->getText();

                    $replySituation = Situation::with('messages.carousels.carouselActions')->where('event_type', 2)->first();

                    if (preg_match('/checkin/', $text)) {
                        $checkin = $this->lineBotService->getParamsFromCheckin($text);
                        $this->customerShopRepository->store($customer, $checkin);

                        if (!is_null($replySituation)) {
                            foreach ($replySituation->messages as $message) {
                                $this->lineBotService->reply($reply_token, $message, $line_token);
                            }
                        }

                        $question = Situation::with('messages.carousels.carouselActions')->where('event_type', 3)->first();
                        foreach ($question->messages as $index => $message) {
                            if ($message->type === 1) {
                                $this->lineBotService->push($line_token, $message);

                                if (isset($question->messages[$index + 1])) {
                                    $this->lineBotService->push($line_token, $question->messages[$index + 1]);
                                    return;
                                }
                            } else {
                                $this->lineBotService->reply($reply_token, $message, $line_token);
                                return;
                            }
                        }

                        return;
                    } else {
                        foreach ($replySituation->messages->where('keyword', $text) as $message) {
                            $this->lineBotService->reply($reply_token, $message, $line_token);
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
                        $this->lineBotService->buildPushMessage($line_token, 'そのメッセージには、すでに回答済みです');
                        return;
                    }

                    $message = Message::find($postbacks['next_message_id']);

                    if (is_null($message)) {
                        break;
                    } else {
                        $this->lineBotService->push($line_token, $message);
                    }

                    return;
                case ($event instanceof UnfollowEvent):
                    $customer = Customer::where('line_token', $line_token)->first();
                    $customer->delete();
                    return;
            }
        }

        return response('OK', 200);
    }
}
