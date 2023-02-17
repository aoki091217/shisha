<?php

namespace App\Services;

use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

class LineBotService
{
    protected $bot;
    private $shop_id;

    public function __construct()
    {
        $client = new CurlHTTPClient(config('services.line.access_token'));
        $this->bot = new LINEBot($client, ['channelSecret' => config('services.line.channel_secret')]);

        $this->shop_id = optional(Shop::first())->shop_id;
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

    /**
     * 選択肢テンプレートメッセージの生成
     */
    public function buildQuestionsTemplate($message, $type, $number)
    {
        $questions = [];
        foreach (config("line.questions.{$type}") as $item) {
            $questions[] = new MessageTemplateActionBuilder($item['string'], $item['string']);
        }

        $confirm = new ButtonTemplateBuilder("アンケートその{$number}", $message, null, $questions);
        $builder = new TemplateMessageBuilder("アンケートその{$number}", $confirm);

        return $builder;
    }

    /**
     * 性別アンケートのプッシュメッセージ
     */
    public function buildSex($line_token)
    {
        $message = '性別についてお聞かせください。';

        $builder = $this->buildQuestionsTemplate($message, 'sex', 1);

        $this->bot->pushMessage($line_token, $builder);
    }

    /**
     * 年代アンケートのプッシュメッセージ
     */
    public function buildGeneration($line_token)
    {
        $message = '年代についてお聞かせください。';

        $builder = $this->buildQuestionsTemplate($message, 'generation', 2);

        $this->bot->pushMessage($line_token, $builder);
    }

    /**
     * どうやって知ったかのアンケートのプッシュメッセージ
     */
    public function buildReason($line_token)
    {
        $message = '来店経路についてお聞かせください。';

        $builder = $this->buildQuestionsTemplate($message, 'reason', 3);

        $this->bot->pushMessage($line_token, $builder);
    }

    public function getIsReplied($type, $text)
    {
        return collect(config("line.questions.{$type}"))->contains(function ($item) use ($text) {
            return $item['string'] === $text;
        });
    }

    public function getQuestionValue($type, $text)
    {
        $item = $this->firstQuestions($type, $text);

        return $item['value'];
    }

    public function firstQuestions($type, $text)
    {
        return collect(config("line.questions.{$type}"))->filter(function ($item) use ($text) {
            return $item['string'] === $text;
        })->first();
    }

    public function createUri($shop_id = null)
    {
        $line_id = urlencode(config('services.line.account_id'));
        $message = $this->getEncodeMessage($shop_id);
        $uri = "https://line.me/R/oaMessage/@{$line_id}/?{$message->encode}";

        return (object) [
            'uri' => $uri,
            'message' => $message
        ];
    }

    private function getEncodeMessage($shop_id)
    {
        $shop = Shop::find($shop_id);
        if (is_null($shop_id) || is_null($shop)) {
            $shop_id = $this->shop_id;
        }
        $now = Carbon::now()->format('Y-m-d_H:i:s');
        $message = config('line.message');
        $encode = urlencode(sprintf('%s%s%s&%s', $message, 'checkin:', "shop_id={$shop_id}", "datetime={$now}"));

        return (object) [
            'native' => sprintf('%s%s&%s', 'checkin:', "shop_id={$shop_id}", "datetime={$now}"),
            'encode' => $encode
        ];
    }

    public function getParamsFromCheckin($text)
    {
        $checkin = Str::after($text, 'checkin:');
        $shop_id = Str::between($checkin, 'shop_id=', '&');
        $datetime = explode('_', Str::after($checkin, 'datetime='));
        $visit = Carbon::parse($datetime[0])->setTimeFromTimeString($datetime[1])->format('Y-m-d H:i:s');

        return [
            'shop_id' => $shop_id,
            'visit' => $visit
        ];
    }
}

?>
