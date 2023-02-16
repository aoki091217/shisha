<nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm p-3 px-5">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="{{ auth()->check() ? route('home.index') : '' }}">
            Shisha Cafe&Bar SIN
        </a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                @guest
                    @if (Route::has('registerForm'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('loginForm') }}">ログイン</a>
                        </li>
                    @endif

                    @if (Route::has('loginForm'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('registerForm') }}">登録</a>
                        </li>
                    @endif
                @else
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userDropdown" aria-controls="userDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div id="userDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdownMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="userDropdownMenu">
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        サインアウト
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                @endguest
            </ul>
        </div>
    </div>
</nav>
