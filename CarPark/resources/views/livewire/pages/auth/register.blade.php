<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold text-gray-900">Izveidot kontu</h1>
    <p class="mt-1 text-sm text-gray-500">Reģistrējieties, lai rezervētu automašīnas un autostāvvietas.</p>

    <form wire:submit="register" class="mt-8 space-y-5">
        <x-auth.input id="name" type="text" name="name" label="Vārds" wire:model="name"
                      autocomplete="name" required autofocus :messages="$errors->get('name')" />

        <x-auth.input id="email" type="email" name="email" label="E-pasts" wire:model="email"
                      autocomplete="username" placeholder="vards@epasts.lv" required
                      :messages="$errors->get('email')" />

        <x-auth.input id="password" type="password" name="password" label="Parole" wire:model="password"
                      autocomplete="new-password" required :messages="$errors->get('password')" />

        <x-auth.input id="password_confirmation" type="password" name="password_confirmation" label="Apstiprināt paroli"
                      wire:model="password_confirmation" autocomplete="new-password" required
                      :messages="$errors->get('password_confirmation')" />

        <x-auth.button class="w-full">Reģistrēties</x-auth.button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Jau ir konts?
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-brand-700 hover:text-brand-900">Pieslēgties</a>
    </p>
</div>
