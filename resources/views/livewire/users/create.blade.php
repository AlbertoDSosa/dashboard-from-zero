<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{layout, rules, state, computed};

layout('layouts.app');

state([
    'authUser' => fn() => Auth::user()
])->locked();

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'role' => '',
    'active' => false
]);

$roles = computed(function () {
   return Role::when($this->authUser->cannot('assign system roles'), function ($query) {
        $query->where('is_from_system', false);
   })->get();
});

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
    'role' => ['required', 'string', 'exists:roles,name'],
    'active' => ['required', 'boolean']
])->messages([
    'name.required' => 'The name field is required',
    'email.required' => 'The email field is required',
    'password.required' => 'The password field is required',
    'role.required' => 'The role field is required',
    'active.required' => 'The active field is required',
    'email.email' => 'This is not a valid email',
    'email.unique' => 'This is not a valid email',
    'password.min' => 'The password must have at least 8 characters',
    'role.exists' => 'It is not a valid role',
]);

$create = function () {
    if($this->authUser->cannot('create users')) {
        abort(400);
    }

    $validated = $this->validate();

    $selectedRole = Role::where('name', $validated['role'])->first();

    if($selectedRole->is_from_system && $this->authUser->cannot('assign system roles')) {
        abort(400);
    }

    $validated['password'] = Hash::make($validated['password']);

    $user = User::create($validated);

    $user->assignRole($validated['role']);

    if($user->active) {
        event(new Registered($user));
    }

    $this->redirect(route('users', absolute: false), navigate: true);
};

?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('User Create') }}
    </h2>
</x-slot>

<div class="pt-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

        <form wire:submit="create" class="max-w-xl">
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <div class="flex gap-6 mt-4">
                <div class="flex-1">
                    <x-input-label for="role" :value="__('Role')" />
                    <select wire:model="role" id="role" name="role" class="form-control" id="role">
                        <option value="" selected disabled>
                            {{ __('Select Role') }}
                        </option>
                        @foreach($this->roles as $role)
                            <option value="{{ $role->name }}">
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2"/>
                </div>
                <div>
                    <x-input-label class="mr-4" for="active" :value="__('Active')" />
                    <input
                        wire:model="active"
                        class="me-2 mt-4 w-8 appearance-none rounded-[0.4375rem] bg-black/25 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1875rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-white after:shadow-switch-2 after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-primary checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ms-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-primary checked:after:shadow-switch-1 checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:outline-none focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-switch-3 focus:before:shadow-black/60 focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-primary checked:focus:bg-primary checked:focus:before:ms-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-switch-3 checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:bg-white/25 dark:after:bg-surface-dark dark:checked:bg-primary dark:checked:after:bg-primary"
                        type="checkbox"
                        role="switch"
                        id="active"
                        name="active"
                    />
                </div>
            </div>
            <div class="flex items-center mt-6">
                <x-primary-button class="ms-4">
                    {{ __('Send') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
