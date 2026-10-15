<?php

use App\Http\Responses\LoginResponse;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest', ['wide' => true])] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirect(LoginResponse::url(), navigate: true);
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold text-gray-900">Pieteikties</h1>
    <p class="mt-1 text-sm text-gray-500">Laipni lūdzam atpakaļ! Ievadiet savus datus, lai turpinātu.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form wire:submit="login" class="mt-8 space-y-5">
        <x-auth.input id="email" type="email" name="email" label="E-pasts" wire:model="form.email"
                      autocomplete="username" placeholder="vards@epasts.lv" required autofocus
                      :messages="$errors->get('form.email')" />

        <x-auth.input id="password" type="password" name="password" label="Parole" wire:model="form.password"
                      autocomplete="current-password" required
                      :messages="$errors->get('form.password')">
            <x-slot:action>
                <a href="{{ route('password.request') }}" wire:navigate class="text-sm font-medium text-brand-700 hover:text-brand-900">Aizmirsi paroli?</a>
            </x-slot:action>
        </x-auth.input>

        <label for="remember" class="inline-flex items-center">
            <input wire:model="form.remember" id="remember" type="checkbox" name="remember"
                   class="rounded border-gray-300 text-brand-700 shadow-sm focus:ring-brand-700">
            <span class="ms-2 text-sm text-gray-600">Atcerēties mani</span>
        </label>

        <x-auth.button class="w-full">Ienākt</x-auth.button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Nav konta?
        <a href="{{ route('register') }}" wire:navigate class="font-semibold text-brand-700 hover:text-brand-900">Reģistrēties</a>
    </p>
</div>
