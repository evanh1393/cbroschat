<?php

use App\Livewire\ChatComp;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(ChatComp::class)
        ->assertStatus(200);
});
