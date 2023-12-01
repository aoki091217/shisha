<!DOCTYPE html>
<html lang="ja">
    <head>
        <!-- Google Tag Manager -->
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-T2Q4C9V4');
        </script>
        <!-- End Google Tag Manager -->

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>タイトル</title>

        <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/versions/2.22.3/sdk.js"></script>

        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T2Q4C9V4"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->
    </body>

    <script>
        const queryParams = @json($queryParams);
        const timeout = 2000;

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
                    url: @json(config('services.line.ngrok') . '/api/line/save_liff'),
                    method: 'POST',
                    data: {
                        line_token: profile.userId,
                        query_params: queryParams
                    }
                })
                .done(function (response) {
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
