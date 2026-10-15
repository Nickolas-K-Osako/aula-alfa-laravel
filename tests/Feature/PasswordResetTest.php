<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Notification::fake();
});

it('renders both password recovery forms', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('name="email"', false);

    $this->get(route('password.reset', [
        'token' => 'example',
        'email' => 'aluno@example.com',
    ]))
        ->assertOk()
        ->assertSee('value="example"', false)
        ->assertSee('aluno@example.com');
});

it('sends a hashed token and uses the configured origin for the email link', function () {
    config(['app.url' => 'https://faculdade.example']);

    \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
    \Illuminate\Support\Facades\URL::forceScheme('https');
    $user = User::factory()->create();
    $this->withHeader('Host', 'attacker.example')->post('/forgot-password', ['email' => $user->email])
        ->assertRedirect(route('password.request'))->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $stored = DB::table('password_reset_tokens')->where('email', $user->email)->value('token');
        expect($stored)->not->toBe($notification->token);
        expect(Hash::check($notification->token, $stored))->toBeTrue();
        expect($notification->toMail($user)->actionUrl)->toStartWith('https://faculdade.example/reset-password/');

        return true;
    });
});

it('returns the same response for registered unknown and throttled emails', function () {
    $user = User::factory()->create();
    $known = $this->post(route('password.email'), ['email' => $user->email]);
    $message = session('status');
    $known->assertRedirect(route('password.request'));
    $this->post(route('password.email'), ['email' => 'unknown@example.com'])
        ->assertRedirect(route('password.request'))->assertSessionHas('status', $message);
    $this->post(route('password.email'), ['email' => $user->email])
        ->assertSessionHas('status', $message);
    Notification::assertSentToTimes($user, ResetPassword::class, 1);
});

it('validates the recovery email', function () {
    $this->post(route('password.email'), ['email' => 'invalid'])->assertSessionHasErrors('email');
    Notification::assertNothingSent();
});

it('changes the password rotates remember token emits an event and consumes the token', function () {
    Event::fake([PasswordReset::class]);
    $user = User::factory()->create(['remember_token' => 'old-token']);
    $token = Password::createToken($user);
    $input = ['email' => $user->email, 'token' => $token,
        'password' => 'new-password-123', 'password_confirmation' => 'new-password-123'];
    $this->post(route('password.update'), $input)->assertRedirect(route('password.request'))
        ->assertSessionHasNoErrors();
    expect(Hash::check($input['password'], $user->fresh()->password))->toBeTrue();
    expect($user->fresh()->remember_token)->not->toBe('old-token');
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    Event::assertDispatched(PasswordReset::class);
    $this->post(route('password.update'), $input)->assertSessionHasErrors('email');
});

it('rejects invalid expired or mismatched tokens without changing the password', function (string $kind) {
    $user = User::factory()->create();
    $original = $user->password;
    $token = Password::createToken($user);
    $email = $user->email;
    if ($kind === 'invalid') {
        $token = 'invalid';
    } elseif ($kind === 'expired') {
        $this->travel(61)->minutes();
    } else {
        $email = User::factory()->create()->email;
    }
    $this->post(route('password.update'), ['email' => $email, 'token' => $token,
        'password' => 'new-password-123', 'password_confirmation' => 'new-password-123'])
        ->assertSessionHasErrors('email');
    expect($user->fresh()->password)->toBe($original);
})->with(['invalid', 'expired', 'mismatched']);

it('requires a token email and a confirmed password of at least eight characters', function (array $override, string $field) {
    $user = User::factory()->create();
    $original = $user->password;
    $input = array_replace(['email' => $user->email, 'token' => Password::createToken($user),
        'password' => 'new-password-123', 'password_confirmation' => 'new-password-123'], $override);
    $this->post(route('password.update'), $input)->assertSessionHasErrors($field);
    expect($user->fresh()->password)->toBe($original);
    expect(Password::tokenExists($user, $input['token'] ?: 'invalid'))->toBe($field !== 'token');
})->with([
    'missing token' => [['token' => ''], 'token'],
    'invalid email' => [['email' => 'invalid'], 'email'],
    'short password' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'mismatched confirmation' => [['password_confirmation' => 'different'], 'password'],
]);

it('limits recovery and reset requests', function (string $route) {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route($route), [])->assertStatus(302);
    }
    $this->post(route($route), [])->assertStatus(429);
})->with(['password.email', 'password.update']);
