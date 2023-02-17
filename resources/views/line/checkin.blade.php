<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

        <!-- Scripts -->
        @vite([
            'resources/sass/app.scss',
            'resources/js/app.js',
            'resources/css/line/checkin.css'
        ])
    </head>
    <body>
        <main>
            <div class="main-contents">
                <div class="container rounded-2 p-3 h-100">
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <div class="col-11 d-flex justify-content-center">
                            <div class="img-wrapper">
                                <img src="{{ asset('images/logo.png') }}" alt="">
                            </div>
                        </div>
                        <div class="col-11 border rounded-1 p-3 bg-white">
                            <div>いらっしゃいませ。</div>
                            <br>
                            <div>本日はShisha Cafe&Bar SINへお越しくださり、誠にありがとうございます。</div>
                            <div>お手数ではございますが、下記のボタンを押してチェックインを行ってください。</div>
                            <a href="{{ $messages->uri }}" class="btn btn-sm btn-success w-100 mt-3">チェックイン</a>
                        </div>
                        <div class="col-11 border rounded-1 p-3 mt-4 text-secondary">
                            <div>ボタンを押すとLINEの画面が開きます。</div>
                            <div>友達登録がまだの方は、登録画面が表示されますので、登録をお願いします。</div>
                            <div>完了後、公式アカウントとのトーク画面が開き、チェックイン用のトークンが自動で入力された状態になっています。</div>
                            <div>送信ボタンを押して、そのトークンを送信し、チェックインを完了させてください。</div>
                        </div>
                        <div class="col-11 mt-3">
                            <details>
                                <summary>PCをお使いの方</summary>
                                公式アカウントを友達登録していただいたのち、以下のメッセージをコピペし、公式アカウントとのチャットで送信してください。

                                <div class="w-100 text-wrap">{{ $messages->message->native }}</div>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
