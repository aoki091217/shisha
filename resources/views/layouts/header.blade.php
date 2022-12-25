<nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm p-3 px-5">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="{{ route('home.index') }}">
            shisha SIN
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userDropdown" aria-controls="userDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="userDropdown">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdownMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        userName
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="userDropdownMenu">
                        <li><a class="dropdown-item" href="#">サインアウト</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
