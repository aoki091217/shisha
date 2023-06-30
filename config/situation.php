<?php

return [
    'event_type' => [
        'follow' => '友達追加',
        'text' => 'メッセージの受信',
        'question' => 'アンケート'
    ],
    'message_type' => [
        'text' => 'テキストのみ',
        'carousel' => 'カルーセル'
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
