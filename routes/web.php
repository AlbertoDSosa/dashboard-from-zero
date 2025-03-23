<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';


Route::middleware(['auth'])->group(function () {
    Volt::route('users', 'users.list')
        ->name('users');
    Volt::route('users/create', 'users.create')
        ->name('users.create');
});


Route::any('{any}', function () {
    if(request()->user()) {
        return back();
    }

    return redirect('login');
})->where('any', '.*');
