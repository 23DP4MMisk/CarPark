<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('admin/bookings', App\Livewire\Admin\BookingList::class)
    ->middleware(['auth', 'admin'])
    ->name('admin.bookings');

Route::post('logout', function (App\Livewire\Actions\Logout $logout) {
    $logout();

    return redirect('/');
})->middleware('auth')->name('logout');

require __DIR__.'/auth.php';
