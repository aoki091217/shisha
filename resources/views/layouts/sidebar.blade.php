<div class="p-3 sidebar shadow bg-white">
    <ul class="nav nav-pills flex-column mb-auto gap-1">
        <li>
            <a href="{{ route('home.index') }}" class="nav-link {{ $routeService->getActiveForTab('home.*') }}">
                <i class="fa-solid fa-house"></i>
                ホーム
            </a>
        </li>
        <li>
            <a href="{{ route('shop.index') }}" class="nav-link {{ $routeService->getActiveForTab('shop.*') }}">
                <i class="fa-solid fa-shop"></i>
                店舗
            </a>
        </li>
        <li>
            <a href="{{ route('member.index') }}" class="nav-link {{ $routeService->getActiveForTab('member.*') }}">
                <i class="fa-solid fa-people-roof"></i>
                スタッフ
            </a>
        </li>
        <li>
            <a href="{{ route('bland.index') }}" class="nav-link {{ $routeService->getActiveForTab('bland.*') }}">
                <i class="fa-solid fa-building"></i>
                ブランド
            </a>
        </li>
        <li>
            <a href="{{ route('flavor.index') }}" class="nav-link {{ $routeService->getActiveForTab('flavor.*') }}">
                <i class="fa-solid fa-leaf"></i>
                フレーバー
            </a>
        </li>
        <li>
            <a href="{{ route('mix.index') }}" class="nav-link {{ $routeService->getActiveForTab('mix.*') }}">
                <i class="fa-solid fa-bong"></i>
                ミックス
            </a>
        </li>
        <li>
            <a href="{{ route('customer.index') }}" class="nav-link {{ $routeService->getActiveForTab('customer.*') }}">
                <i class="fa-solid fa-person-circle-check"></i>
                顧客
            </a>
        </li>
        <li>
            <a href="{{ route('bill.index') }}" class="nav-link {{ $routeService->getActiveForTab('bill.*') }}">
                <i class="fa-solid fa-yen-sign"></i>
                会計
            </a>
        </li>
        @if (auth()->user()->role_id === 1)
        <li>
            <a href="{{ route('user.index') }}" class="nav-link {{ $routeService->getActiveForTab('user.*') }}">
                <i class="fa-solid fa-users-gear"></i>
                ユーザー
            </a>
        </li>
        <li>
            <a href="{{ route('situation.index') }}" class="nav-link {{ $routeService->getActiveForTab('situation.*') }}">
                <i class="fa-regular fa-comment-dots"></i>
                メッセージ
            </a>
        </li>
        @endif
    </ul>
</div>
