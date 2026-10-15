<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function forgotPassword(): View
    {
        return view('user.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $input = $request->validate(['email' => 'required|string|email|max:255']);

        Password::sendResetLink($input);

        // Do not disclose whether this email belongs to an account.
        return redirect()->route('password.request')->with('status',
            'Se este e-mail estiver cadastrado, você receberá um link para redefinir a senha.'
        );
    }

    public function resetPassword(Request $request, string $token): View
    {
        return view('user.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $input = $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset($input, function (User $user, string $password): void {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PasswordReset) {
            return redirect()->route('password.request')
                ->with('status', 'Senha redefinida com sucesso. Você já pode usar a nova senha.');
        }

        return back()->withErrors([
            'email' => 'O link é inválido ou expirou. Solicite um novo link de recuperação.',
        ])->withInput($request->only('email'));
    }

    public function index()
    {
        $users = User::paginate(5);

        return view('user.index', [
            'users' => $users,
            'title' => 'Lista de usuario do Sistema',
        ]);
    }






}
