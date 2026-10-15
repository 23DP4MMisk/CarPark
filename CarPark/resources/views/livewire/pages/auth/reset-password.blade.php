<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold text-gray-900">Jauna parole</h1>
    <p class="mt-1 text-sm text-gray-500">Izveidojiet jaunu paroli savam kontam.</p>

    <form wire:submit="resetPassword" class="mt-8 space-y-5">
        <x-auth.input id="email" type="email" name="email" label="E-pasts" wire:model="email"
                      autocomplete="username" required autofocus :messages="$errors->get('email')" />

        <x-auth.input id="password" type="password" name="password" label="Jaunā parole" wire:model="password"
                      autocomplete="new-password" required :messages="$errors->get('password')" />

        <x-auth.input id="password_confirmation" type="password" name="password_confirmation" label="Apstiprināt paroli"
                      wire:model="password_confirmation" autocomplete="new-password" required
                      :messages="$errors->get('password_confirmation')" />

        <x-auth.button class="w-full">Atiestatīt paroli</x-auth.button>
    </form>
</div>
