<?php

return [
    'connections' => [
        'main' => [
            // ハッシュ化するときの魔法の粉
            'salt' => env('HASHIDS_SALT'),
            // ハッシュ値で使いたい文字列
            'alphabet' => 'idGLNNcceytg3l6B1lqYMn8BrNMomMdU',
            // ハッシュ値の長さ
            'length' => 6,
        ],
        // ...
    ]
]

?>
