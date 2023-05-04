<?php

return [
    'event_type' => [
        'follow' => '友達追加',
        'text' => 'メッセージの受信',
        // 'question' => 'アンケート',
        'unfollow' => 'ブロック'
    ],
    'message_type' => [
        'text' => 'テキストのみ',
        'buttons' => 'ボタン',
        // 'carousel' => 'カルーセル',
        // 'image_carousel' => '画像のみのカルーセル'
    ],
    'send_type' => [
        'push' => 'プッシュメッセージ',
        'reply' => 'リプライメッセージ'
    ],
    'action_type' => [
        'message' => 'メッセージ',
        'uri' => 'リンク'
    ],
    'default' => [
        'text' => [
            'type' => 'text',
            'text' => null
        ],
        'buttons' => [
            'type' => 'template',
            'altText' => null,
            'template' => [
                'type' => 'buttons',
                'thumbnailImageUrl' => null,
                'title' => null,
                'text' => null,
                'actions' => []
            ]
        ]
    ]
];

?>
