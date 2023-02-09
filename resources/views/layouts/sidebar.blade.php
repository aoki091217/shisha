<div class="p-3 sidebar shadow">
    <ul class="nav nav-pills flex-column mb-auto">
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
                <i class="fa-solid fa-bong"></i>
                フレーバー
            </a>
        </li>
        <li>
            <a href="{{ route('bill.index') }}" class="nav-link {{ $routeService->getActiveForTab('bill.*') }}">
                <i class="fa-solid fa-yen-sign"></i>
                会計
            </a>
        </li>
    </ul>
</div>
