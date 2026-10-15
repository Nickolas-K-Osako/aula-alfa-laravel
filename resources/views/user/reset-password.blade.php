@extends('layouts.default')
@section('title', 'Redefinir senha')
@section('classe-body', 'login-page bg-body-secondary')
@section('content')
    <main class="login-box">
        <h1 class="login-logo"><a href="{{ url('/') }}"><b>Redefinir senha</b></a></h1>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Informe e confirme sua nova senha, com pelo menos 8 caracteres.</p>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p class="mb-0">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <form action="{{ route('password.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}" />
                    <label class="visually-hidden" for="email">E-mail</label>
                    <div class="input-group mb-3">
                        <input id="email" name="email" type="email" class="form-control" placeholder="E-mail"
                            value="{{ old('email', $email) }}" autocomplete="email" maxlength="255" required />
                        <div class="input-group-text"><span class="bi bi-envelope" aria-hidden="true"></span></div>
                    </div>
                    <label class="visually-hidden" for="password">Nova senha</label>
                    <div class="input-group mb-3">
                        <input id="password" name="password" type="password" class="form-control" placeholder="Nova senha"
                            autocomplete="new-password" minlength="8" required />
                        <div class="input-group-text"><span class="bi bi-lock-fill" aria-hidden="true"></span></div>
                    </div>
                    <label class="visually-hidden" for="password_confirmation">Confirmar nova senha</label>
                    <div class="input-group mb-3">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                            placeholder="Confirmar nova senha" autocomplete="new-password" minlength="8" required />
                        <div class="input-group-text"><span class="bi bi-lock-fill" aria-hidden="true"></span></div>
                    </div>
                    <div class="d-grid gap-2"><button type="submit" class="btn btn-primary">Redefinir senha</button></div>
                </form>
                <p class="mt-3 mb-0"><a href="{{ route('password.request') }}">Solicitar novo link</a></p>
            </div>
        </div>
    </main>
@endsection
