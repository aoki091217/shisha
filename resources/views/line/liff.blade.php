<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>タイトル</title>

        <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/versions/2.22.3/sdk.js"></script>

        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    </head>
    <body>
        <h2 style="color: red">LIFFリダイレクト中</h2>
    </body>

    <script>
        $(function () {
            const liffRequest = @json($request->all());

            liff.init({
                liffId: '1657897256-AXXo9xOq',
            }).then(() => {
                let accessToken = '';
                let queryParam = '';
                console.log(liffRequest);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                    },
                    url: @json(config('services.line.ngrok') . '/api/line/save_liff'),
                    method: 'POST',
                    data: {
                        queryParam: liffRequest
                    }
                })
                .done(function (data) {
                    console.log(data);
                })
                .fail(function (error) {
                    console.log(error);
                })

                // liff.openWindow({
                //     url: `https://line.me/R/oaMessage/${}/${}`
                // });


            })
            .catch((err) => {
                console.log(err.code, err.message);
            });


        });


    </script>
</html>
