<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold text-gray-900">Aizmirsi paroli?</h1>
    <p class="mt-1 text-sm text-gray-500">Nekas. Ievadiet e-pasta adresi, un mēs nosūtīsim saiti jaunas paroles izveidei.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="mt-8 space-y-5">
        <x-auth.input id="email" type="email" name="email" label="E-pasts" wire:model="email"
                      placeholder="vards@epasts.lv" required autofocus :messages="$errors->get('email')" />

        <x-auth.button class="w-full">Nosūtīt atiestatīšanas saiti</x-auth.button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-brand-700 hover:text-brand-900">Atpakaļ uz pieteikšanos</a>
    </p>
</div>
