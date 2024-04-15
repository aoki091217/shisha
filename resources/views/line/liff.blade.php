<!DOCTYPE html>
<html lang="ja">
    <head>
        <script>
            @if ($code->exists)
            {!! e($code->getScript()) !!}
            @endif
        </script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title></title>

        <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/versions/2.22.3/sdk.js"></script>

        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    </head>

    @php
        $domain = app()->isProduction() ? env('APP_URL') : env('APP_NGROK');
    @endphp

    <script>
        const queryParams = @json($request->session()->get('query_params'));
        const timeout = 2000;
        const domain = @json($domain);

        setTimeout(() => {
            liff.init({
                liffId: @json(config('services.line.liff_id')),
            }).then(() => {
                if (liff.isLoggedIn() === false) {
                    liff.login({})
                }
                getUserInfo(queryParams);
            })
            .catch((err) => {
                console.log(err.code, err.message);
            });
        }, timeout);

        const getUserInfo = (queryParams) => {
            liff.getProfile().then(profile => {

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                    },
                    url: `${domain}/api/line/save_liff`,
                    method: 'POST',
                    data: {
                        line_token: profile.userId,
                        query_params: queryParams
                    }
                })
                .done(function (response) {
                    console.log(response);
                    window.location.href = @json($lineUrl);
                })
                .fail(function (error) {
                    console.log(error);
                })

            })
            .catch((error) => {
                console.log(error);
            })
        }
    </script>
</html>
