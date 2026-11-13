<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureAdmin;

// Rotas públicas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

// Cadastro público
Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/store', [UserController::class, 'store'])->name('users.store');

// Rotas que exigem autenticação
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Rotas administrativas
Route::prefix('admin')->middleware(EnsureAdmin::class)->group(function () {

    // Listagem de usuários
    Route::get('/users', [UserController::class, 'index'])
        ->name('admin.users.index');

    // Formulário de edição
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])
        ->name('admin.user.edit');

    // Atualização de usuário
    Route::put('/users/{id}', [UserController::class, 'update'])
        ->name('admin.user.update');

});

Route::prefix('admin')->middleware(EnsureAdmin::class)->group(function () {

    Route::get('/users', [UserController::class, 'index'])
        ->name('admin.users.index');

    Route::get('/users/{id}/edit', [UserController::class, 'edit'])
        ->name('admin.user.edit');

    Route::put('/users/{id}', [UserController::class, 'update'])
        ->name('admin.user.update');

   

});
