<div class="sidebar shadow bg-white">
    <ul class="nav nav-pills flex-column mb-auto gap-1">
        <li>
            <a href="{{ route('home.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('home.*') }}">
                <i class="fa-solid fa-house"></i>
                ホーム
            </a>
        </li>
        <li>
            <a href="#" class="nav-link py-1">
                <i class="fa-regular fa-newspaper"></i>
                レポート
            </a>
            <ul style="list-style: none" class="my-1">
                <li>
                    <a href="{{ route('report.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('report.index') }}">
                        <i class="fa-regular fa-newspaper"></i>
                        サマリー
                    </a>
                </li>
                <li>
                    <a href="{{ route('report.code') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('report.code') }}">
                        <i class="fa-regular fa-newspaper"></i>
                        コード分析
                    </a>
                </li>
                <li>
                    <a href="{{ route('report.customer') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('report.customer') }}">
                        <i class="fa-regular fa-newspaper"></i>
                        顧客分析
                    </a>
                </li>
            </ul>
        </li>
        @if (auth()->user()->role_id === 1)
        <li>
            <a href="{{ route('shop.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('shop.*') }}">
                <i class="fa-solid fa-shop"></i>
                店舗
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('member.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('member.*') }}">
                <i class="fa-solid fa-people-roof"></i>
                スタッフ
            </a>
        </li>
        <li>
            <a href="{{ route('bland.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('bland.*') }}">
                <i class="fa-solid fa-building"></i>
                ブランド
            </a>
        </li>
        <li>
            <a href="{{ route('flavor.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('flavor.*') }}">
                <i class="fa-solid fa-leaf"></i>
                フレーバー
            </a>
        </li>
        <li>
            <a href="{{ route('mix.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('mix.*') }}">
                <i class="fa-solid fa-bong"></i>
                ミックス
            </a>
        </li>
        @if (in_array(auth()->user()->role_id, [1, 2]))
        <li>
            <a href="{{ route('customer.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('customer.*') }}">
                <i class="fa-solid fa-person-circle-check"></i>
                顧客
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('bill.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('bill.*') }}">
                <i class="fa-solid fa-yen-sign"></i>
                会計
            </a>
        </li>
        @if (in_array(auth()->user()->role_id, [1, 2]))
        <li>
            <a href="{{ route('user.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('user.*') }}">
                <i class="fa-solid fa-users-gear"></i>
                ユーザー
            </a>
        </li>
        <li>
            <a href="{{ route('situation.follow.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('situation.follow.*') }}">
                <i class="fa-solid fa-user-plus"></i>
                友達追加メッセージ
            </a>
        </li>
        <li>
            <a href="{{ route('situation.question.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('situation.question.*') }}">
                <i class="fa-solid fa-clipboard-question"></i>
                アンケート
            </a>
        </li>
        <li>
            <a href="{{ route('situation.reply.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('situation.reply.*') }}">
                <i class="fa-solid fa-reply"></i>
                応答メッセージ
            </a>
        </li>
        <li>
            <a href="{{ route('situation.push.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('situation.push.*') }}">
                <i class="fa-regular fa-comment-dots"></i>
                プッシュメッセージ
            </a>
        </li>
        <li>
            <a href="{{ route('code.index') }}" class="nav-link py-1 {{ $routeService->getActiveForTab('code.*') }}">
                <i class="fa-solid fa-barcode"></i>
                コード発行
            </a>
        </li>
        @endif
    </ul>
</div>
