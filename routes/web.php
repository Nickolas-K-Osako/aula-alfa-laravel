<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/user', function(){
//     return ['hello word','quarto periodo'];
// });

Route::get('/user',[User::class, 'show_user']);

Route::get('/users',[User::class, 'getAll']);

Route::get('/user/{id}', function($id){
    $user = User::find($id);
    dd($user);

});

Route::get('/user-create',function(){
  $user =  User::create([
    'name' => 'Cristiano',
    'email' => 'email@email.com',
    'password' => '12345678'
  ]);

  dd($user);

});

Route::get('/user-update/{id}',function($id){
  $user = User::find($id);

  $user->update([
    'name' => 'Cristiano Herculano Da Silva',
    'email' => 'email@email.com',
    'password' => '12345678'
  ]);

});

Route::get('/user-delete/{id}', function($id){
   $user = User::find($id);
   $user->delete();
   return response()->json(['message'=> 'Usuario deletado']);
});

Route::get('/user-profile/{id}', function($id){
    $user = User::with('profile')->find($id);
    dd($user);

});

Route::get('/user-with-profile', function(){
   $user = User::create([
    'name' => 'Cristiano Herculano Da Silva',
    'email' => 'email@email.com',
    'password' => '12345678'
   ]);

   $profile = $user->profile()->create([
    'type' => 'ADMIN',
    'description' => 'Administrador de servidor'
   ]);

   dd($user->load('profile'));

});

Route::get('/admin/users', [UserController::class, 'index']);

// Recuperação de senha
Route::get('/forgot-password', [UserController::class, 'forgotPassword'])
    ->name('password.request');

Route::post('/forgot-password', [UserController::class, 'sendResetLink'])
    ->middleware('throttle:5,1')
    ->name('password.email');

Route::get('/reset-password/{token}', [UserController::class, 'resetPassword'])
    ->name('password.reset');

Route::post('/reset-password', [UserController::class, 'changePassword'])
    ->middleware('throttle:5,1')
    ->name('password.update');
