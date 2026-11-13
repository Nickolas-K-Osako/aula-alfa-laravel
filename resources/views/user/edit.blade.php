@extends('layouts.default')
@section('classe-body', 'register-page', 'register-page bg-body-secondary')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <main class="register-box w-100 mx-auto" style="max-width: 900px;">
                <h1 class="register-logo text-center">
                    <a href="../index2.html"><b>Bem vindo</b></a>
                </h1>
                <div class="card">
                    <div class="card-body register-card-body">
                        <p class="register-box-msg">Preencha os campos para se registrar</p>

                        <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-header">
                        Dados do usuário
                    </div>
                    <div class="card-body">
                        <label class="visually-hidden" for="name">Nome</label>
                        <div class="input-group mb-3">
                            <input id="name" name="name" type="text"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Nome"
                                value="{{ $user->name }}" />
                            <div class="input-group-text">
                                <span class="bi bi-person"></span>
                            </div>
                        </div>
                        <label class="visually-hidden" for="email">Email</label>
                        <div class="input-group mb-3">
                            <input id="email" name="email" type="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                value="{{ $user->email }}" />
                            <div class="input-group-text">
                                <span class="bi bi-envelope"></span>
                            </div>
                            <div class="invalid-feedback">
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer"></div>



                    <!--begin::Row-->
                    <div class="row" flex="true" style="display: flex; justify-content: center;">

                        <!-- /.col -->
                        <div class="col-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!--end::Row-->
                </form>
                <br>

                        <!-- /.social-auth-links -->

                    </div>
                    <!-- /.register-card-body -->
                </div>
            </main>
        </div>
    </div>
@endsection
