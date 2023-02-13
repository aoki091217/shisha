<?php

return [
    'questions' => [
        'sex' => [
            'male' => [
                'value' => 1,
                'string' => '男性'
            ],
            'female' => [
                'value' => 2,
                'string' => '女性'
            ]
        ],
        'generation' => [
            'twenty' => [
                'value' => 20,
                'string' => '20代'
            ],
            'thirty' => [
                'value' => 30,
                'string' => '30代'
            ],
            'fourty' => [
                'value' => 40,
                'string' => '40代'
            ],
            'fifty' => [
                'value' => 50,
                'string' => '50代'
            ],
        ],
        'reason' => [
            'walk' => [
                'value' => 1,
                'string' => '徒歩'
            ],
            'taxi' => [
                'value' => 2,
                'string' => 'タクシー'
            ],
            'bus' => [
                'value' => 3,
                'string' => 'バス'
            ],
            'train' => [
                'value' => 4,
                'string' => '電車'
            ],
        ]
    ],
    'message' => 'チェックインのために、本メッセージをそのまま送信してください。'
];

?>
