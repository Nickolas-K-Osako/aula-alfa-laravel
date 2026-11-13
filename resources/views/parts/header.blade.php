<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <span class="navbar-brand">
            Sistema Laravel
        </span>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span>{{ auth()->user()->name }}</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-outline-danger btn-sm">
                    Sair
                </button>
            </form>
        </div>
    </div>
</nav>
