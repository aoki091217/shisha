<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SendMessage;
use App\Models\Situation;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerShopRepository;
use App\Services\LineBotService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
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
                    $templates = Situation::with('messages.messageActions')->where('event_type', 1)->get()->pluck('messages')->flatten();
                    foreach ($templates as $template) {
                        $this->lineBotService->push($line_token, $template);
                    }

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
                    return;
                case ($event instanceof TextMessage):
                    $text = $event->getText();

                    $templates = Situation::with('messages.messageActions')->where('event_type', 2)->get()->pluck('messages')->flatten();

                    if (preg_match('/checkin/', $text)) {
                        $checkin = $this->lineBotService->getParamsFromCheckin($text);
                        $this->customerShopRepository->store($customer, $checkin);

                        foreach ($templates->where('keyword', 'checkin') as $template) {
                            $this->lineBotService->reply($reply_token, $template);
                        }
                        return;
                    }

                    // if (preg_match('/アンケートを開始/', $text)) {
                    //     $template = $situations->where('event_type', 3)->first()->messages->first();

                    //     $this->lineBotService->saveAnswer($customer->id, $template->id, null, 1);
                    //     $this->lineBotService->reply($reply_token, $template);

                    //     return;
                    // }
                    $send = SendMessage::where('customer_id', $customer->id)->get()->first();
                    if (!is_null($send)) {
                        $templateIndex = 1;
                        foreach ($templates as $index => $template) {
                            if ($template->id === $send->message_id) {
                                $templateIndex = $index + 1;
                                break;
                            }
                        }

                        $templates = $templates->slice($templateIndex);
                    }

                    foreach ($templates as $index => $template) {
                        // $beforeTemplate = $this->lineBotService->getBeforeTemplate($templates, $index);

                        if (Str::contains($text, $template->keyword)) {
                            // $this->lineBotService->saveResponse($customer->id, $template->id, $text);
                            $this->lineBotService->reply($reply_token, $template);
                            $this->lineBotService->saveSend($customer->id, $template->id);

                            return;
                        }

                        // switch ($template->send_type) {
                        //     case 1:
                        //         // push
                        //         $this->lineBotService->push($line_token, $template);
                        //         $this->lineBotService->saveSend($customer->id, $template->id);

                        //         return;
                        //     case 2:
                        //         // reply
                        //         // $this->lineBotService->saveResponse($customer->id, $beforeTemplate?->id, $text);
                        //         $this->lineBotService->reply($reply_token, $template);
                        //         $this->lineBotService->saveSend($customer->id, $template->id);

                        //         return;
                        // }
                    }

                    return;
                case ($event instanceof UnfollowEvent):
                    $templates = Situation::with('messages.messageActions')->where('event_type', 3)->get()->pluck('messages')->flatten();
                    foreach ($templates as $template) {
                        switch ($template->send_type) {
                            case 1:
                                $this->lineBotService->push($line_token, $template);
                                break;
                        }
                    }

                    $customer = Customer::where('line_token', $line_token)->first();
                    $customer->delete();
                    return;
            }
        }

        return response('OK', 200);
    }
}
