<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold text-gray-900">Apstipriniet paroli</h1>
    <p class="mt-1 text-sm text-gray-500">Šī ir drošā lietotnes daļa. Lūdzu, apstipriniet savu paroli, lai turpinātu.</p>

    <form wire:submit="confirmPassword" class="mt-8 space-y-5">
        <x-auth.input id="password" type="password" name="password" label="Parole" wire:model="password"
                      autocomplete="current-password" required autofocus :messages="$errors->get('password')" />

        <x-auth.button class="w-full">Apstiprināt</x-auth.button>
    </form>
</div>
