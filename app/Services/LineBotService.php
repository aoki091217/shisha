<?php

namespace App\Services;

use App\Http\Requests\Api\LineRequest;
use App\Models\Customer;
use App\Models\LiffAccess;
use App\Models\Shop;
use Carbon\Carbon;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use Storage;

class LineBotService
{
    protected $bot;
    private Shop $shop;

    public function __construct($id)
    {
        if (is_null($id)) {
            $shop = Shop::first();
        } else {
            $shop = Shop::find($id);
        }

        $client = new CurlHTTPClient($shop->line_token);
        $this->bot = new LINEBot($client, ['channelSecret' => $shop->channel_secret]);

        $this->shop = $shop;
    }

    /**
     * テキストの応答メッセージ
     */
    public function buildReplyMessage($reply_token, $message)
    {
        $builder = new TextMessageBuilder($message);
        $this->bot->replyMessage($reply_token, $builder);
    }

    /**
     * 二者択一形式の応答メッセージ
     */
    public function buildConfirm($message, $reply_token)
    {
        $positive = new MessageTemplateActionBuilder('はい', 'はい');
        $negative = new MessageTemplateActionBuilder('いいえ', 'いいえ');
        $buttons = [$positive, $negative];
        $confirm = new ConfirmTemplateBuilder($message, $buttons);
        $builder = new TemplateMessageBuilder('confirm', $confirm);

        $this->bot->replyMessage($reply_token, $builder);
    }

    /**
     * テキストのプッシュメッセージ
     */
    public function buildPushMessage($line_token, $message)
    {
        $builder = new TextMessageBuilder($message);
        $this->bot->pushMessage($line_token, $builder);
    }

    public function push($line_token, $message)
    {
        switch ($message->type) {
            case 1:
                // テキスト
                $builder = new TextMessageBuilder($message->text);
                break;
            case 2:
                $nextTurn = $message->turn + 1;
                $nextMessage = $message->load('situation.messages')->situation->messages->where('turn', $nextTurn)->first();

                $customer = Customer::where('line_token', $line_token)->first();

                // カルーセル
                $actions = [];
                foreach ($message->carousels as $carousel) {
                    $actions = [];
                    foreach ($carousel->carouselActions as $carouselAction) {
                        $postback = "carousel_id={$carousel->id}&carousel_action_id={$carouselAction->id}&customer_id={$customer->id}&next_message_id={$nextMessage?->id}";

                        $actions[] = new PostbackTemplateActionBuilder(
                            $carouselAction->action,
                            $postback,
                            $carouselAction->action
                        );
                    }

                    $filePath = null;
                    if ($carousel->thumbnail_image_url) {
                        $filePath = Storage::disk('public')->url($carousel->thumbnail_image_url);
                    }

                    $carousels[] = new CarouselColumnTemplateBuilder(
                        $carousel->title,
                        $carousel->text,
                        $filePath,
                        $actions
                    );

                }

                $carouselTemplates = new CarouselTemplateBuilder($carousels);

                $builder = new TemplateMessageBuilder($message->alt_text, $carouselTemplates);
                break;
        }

        $this->bot->pushMessage($line_token, $builder);
    }

    public function reply($reply_token, $message, $line_token)
    {
        switch ($message->type) {
            case 1:
                // text
                $builder = new TextMessageBuilder($message->text);
                break;
            case 2:
                $customer = Customer::where('line_token', $line_token)->first();

                $nextTurn = $message->turn + 1;
                $nextMessage = $message->load('situation.messages')->situation->messages->where('turn', $nextTurn)->first();

                // カルーセル
                $actions = [];
                foreach ($message->carousels as $carousel) {
                    $actions = [];
                    foreach ($carousel->carouselActions as $carouselAction) {
                        $postback = "carousel_id={$carousel->id}&carousel_action_id={$carouselAction->id}&customer_id={$customer->id}&next_message_id={$nextMessage?->id}";

                        $actions[] = new PostbackTemplateActionBuilder(
                            $carouselAction->action,
                            $postback,
                            $carouselAction->action
                        );
                    }

                    $filePath = null;
                    if ($carousel->thumbnail_image_url) {
                        $filePath = Storage::disk('public')->url($carousel->thumbnail_image_url);
                    }

                    $carousels[] = new CarouselColumnTemplateBuilder(
                        $carousel->title,
                        $carousel->text,
                        $filePath,
                        $actions
                    );
                }

                $carouselTemplates = new CarouselTemplateBuilder($carousels);

                $builder = new TemplateMessageBuilder($message->alt_text, $carouselTemplates);
                break;
        }

        $this->bot->replyMessage($reply_token, $builder);
    }

    public function getLineUrl()
    {
        $accountId = urlencode($this->shop->account_id);

        return "https://line.me/R/oaMessage/{$accountId}/";
    }

    public function getLiffId(LineRequest $request)
    {
        $ip = $request->ip();
        $liffAccess = LiffAccess::where('ip', $ip)->where('created_at', now()->subMinutes(1))->orderByDesc('created_at')->first();

        if (is_null($liffAccess)) {
            $liffAccess = new LiffAccess();
            $liffAccess->fill(['ip' => $ip, 'liff_id' => $this->shop->liff_id])->save();
        }

        return $liffAccess->liff_id;
    }

    public function getLineUrlWithMessage()
    {
        $uri = $this->getLineUrl();

        $message = $this->getEncodeParam();
        $uri = "{$uri}?{$message->encode}";

        return (object) [
            'uri' => $uri,
            'message' => $message
        ];
    }

    private function getEncodeParam()
    {
        $shop_id = $this->shop->shop_id;

        $now = Carbon::now()->format('Y-m-d_H:i:s');
        $message = config('line.message');
        $encode = urlencode($message . join('&', ['action=checkin', "shop_id={$shop_id}", "datetime={$now}"]));

        return (object) [
            'native' => join('&', ['action=checkin', "shop_id={$shop_id}", "datetime={$now}"]),
            'encode' => $encode
        ];
    }

    public function getParamsFromCheckin(array $params)
    {
        $datetime = explode('_', $params['datetime']);
        $visited_at = Carbon::parse($datetime[0])->setTimeFromTimeString($datetime[1])->format('Y-m-d H:i:s');

        return [
            'shop_id' => $params['shop_id'],
            'visited_at' => $visited_at
        ];
    }

    public function checkDuplicate(array $params, Customer $customer): bool
    {
        $checkinDatetime = Carbon::parse(join(explode('_', $params['datetime'])));
        $latestCustomerShop = $customer->customerShops->where('shop_id', $params['shop_id'])->where('visited_at', '>', $checkinDatetime->copy()->subHour())->sortByDesc('visited_at')->first();

        return is_null($latestCustomerShop);
    }

    public function getQueryParams(string $query): array
    {
        $params = [];

        foreach (explode('&', $query) as $explode) {
            $params[strstr($explode, '=', true)] = substr($explode, strpos($explode, '=') + 1, strlen($explode));
        }

        return $params;
    }
}

?>
