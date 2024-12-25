<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('chat-show');
    }
    return redirect()->route('register');
})->name('home');

Route::get('chat', \App\Livewire\ChatComp::class)
    ->middleware(['auth', 'verified'])
    ->name('chat-show');

require __DIR__.'/auth.php';
