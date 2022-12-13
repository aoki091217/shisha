<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
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

    public function __construct()
    {
        $client = new CurlHTTPClient(config('services.line.access_token'));
        $this->bot = new LINEBot($client, ['channelSecret' => config('services.line.channel_secret')]);
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
    public function buildConfirm($event, $reply_token)
    {
        $message = "ニックネームは「{$event->getText()}」でよろしいですか？";

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
}

?>
