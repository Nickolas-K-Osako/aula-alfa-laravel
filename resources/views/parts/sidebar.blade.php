<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <span class="brand-text fw-light">
            Menu
        </span>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link">
                        <p>Início</p>
                    </a>
                </li>

                @if(auth()->user()?->profile?->type === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link">
                            <p>Gerenciar usuários</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
