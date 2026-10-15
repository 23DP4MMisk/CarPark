<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold text-gray-900">Apstipriniet e-pastu</h1>
    <p class="mt-1 text-sm text-gray-500">Paldies par reģistrēšanos! Pirms sākat, lūdzu, apstipriniet e-pasta adresi, noklikšķinot uz saites, ko mēs jums nosūtījām. Ja e-pasts nav saņemts, sūtīsim vēlreiz.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 rounded-lg bg-brand-50 px-4 py-3 text-sm font-medium text-brand-900">
            Jauna apstiprinājuma saite ir nosūtīta uz e-pasta adresi, ko norādījāt reģistrācijas laikā.
        </div>
    @endif

    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
        <button type="button" wire:click="logout" class="text-center text-sm font-medium text-gray-600 underline hover:text-gray-900">
            Iziet
        </button>

        <x-auth.button type="button" wire:click="sendVerification" class="w-full sm:w-auto">
            Nosūtīt vēlreiz
        </x-auth.button>
    </div>
</div>
