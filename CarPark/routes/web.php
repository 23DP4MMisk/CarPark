<?php

use Illuminate\Support\Facades\Route;

Route::get('/', App\Livewire\Cars\CarList::class)->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('cars/{car}/book', App\Livewire\Cars\CarBookingForm::class)
    ->middleware('auth')
    ->name('cars.book');

Route::get('parking/{spot}/book', App\Livewire\Parking\ParkingBookingForm::class)
    ->middleware('auth')
    ->name('parking.book');

Route::get('my-bookings', App\Livewire\MyBookings::class)
    ->middleware(['auth'])
    ->name('my-bookings');

Route::get('admin/bookings', App\Livewire\Admin\BookingList::class)
    ->middleware(['auth', 'admin'])
    ->name('admin.bookings');

Route::get('admin/parking', App\Livewire\Admin\ParkingManager::class)
    ->middleware(['auth', 'admin'])
    ->name('admin.parking');

Route::post('logout', function (App\Livewire\Actions\Logout $logout) {
    $logout();

    return redirect('/');
})->middleware('auth')->name('logout');

require __DIR__.'/auth.php';
