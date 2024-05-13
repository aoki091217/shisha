<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shop->name }}</title>
    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/versions/2.22.3/sdk.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"
            integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>
<body>
{{-- TODO: ローディング&フォールバック --}}
<script>
    (function () {
        function liffInit(redirectUri, sessionToken) {
            return liff.init({
                liffId: shop.liffId,
                withLoginOnExternalBrowser: false,
            }).then(function () {
                if (!liff.isLoggedIn()) {
                    liff.login({redirectUri: redirectUri ? redirectUri : location.href});
                    return null;
                }

                return {
                    accessToken: liff.getAccessToken(),
                };
            }).then(function (data) {
                if (!data) {
                    return null;
                }

                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url: @json(route('liff.verify', ['shop_id' => $shop->shop_id])),
                        method: 'POST',
                        data: {
                            accessToken: data.accessToken,
                            sessionToken: sessionToken
                        }
                    }).done(function (res) {
                        resolve(res);
                    }).fail(function (e) {
                        reject(e);
                    })
                });
            }).then(function (res) {
                if (res && res.redirectUri) {
                    location.href = res.redirectUri;
                }
            }).catch(function (e) {
                console.error(e);
            })
        }

        const shop = {
            name: @json($shop->name),
            liffId: @json($shop->liff_id),
        };
        const redirectUri = @json($session?->callbackUrl());
        const sessionToken = @json($session?->session_token);
        liffInit(redirectUri, sessionToken);
    })();
</script>
</body>
</html>
