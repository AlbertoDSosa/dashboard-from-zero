<?php

namespace Tests\Feature\Livewire\Users;

use Livewire\Volt\Volt;
use Tests\TestCase;

class ListTest extends TestCase
{
    public function test_it_can_render(): void
    {
        $component = Volt::test('users.list');

        $component->assertSee('');
    }
}
