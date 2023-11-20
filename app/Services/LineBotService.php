<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Str;
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
    private $shop_id;

    public function __construct($id)
    {
        if (is_null($id)) {
            $shop = Shop::first();
        } else {
            $shop = Shop::find($id);
        }

        $client = new CurlHTTPClient($shop->line_token);
        $this->bot = new LINEBot($client, ['channelSecret' => $shop->channel_secret]);

        $this->shop_id = $shop->shop_id;
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

    public function getLineUrl($shopId = null)
    {
        if (is_null($shopId)) {
            $shop = Shop::first();
        } else {
            $shop = Shop::find($shopId);
        }
        $accountId = urlencode($shop->account_id);
        $message = $this->getEncodeParam($shopId);
        $uri = "https://line.me/R/oaMessage/{$accountId}/?{$message->encode}";

        return (object) [
            'uri' => $uri,
            'message' => $message
        ];
    }

    public function getLiffUrl($shopId = null): string
    {
        $parameter = $this->getEncodeParam($shopId);
        $liffUrl = 'https://liff.line.me/' . config('services.line.liff_id') . "?{$parameter->native}";

        return $liffUrl;
    }

    private function getEncodeParam($shop_id)
    {
        $shop = Shop::find($shop_id);
        if (is_null($shop_id) || is_null($shop)) {
            $shop_id = $this->shop_id;
        }
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
