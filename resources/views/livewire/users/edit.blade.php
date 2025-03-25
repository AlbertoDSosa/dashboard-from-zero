<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use function Livewire\Volt\{state, layout,rules, mount, computed};

layout('layouts.app');

state(['user'])->locked();

rules(fn() => [
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
    'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
    'role' => ['required', 'exists:roles,name'],
    'active' => ['required', 'boolean'],
])->messages([
    'name.required' => 'The name field is required',
    'email.required' => 'The email field is required',
    'email.email' => 'This is not a valid email',
    'email.unique' => 'This is not a valid email',
    'password.min' => 'The password must have at least 8 characters',
    'role.exists' => 'It is not a valid role',
    'active.required' => 'The active field is required',
]);

state([
    'name' => fn() => $this->user->name,
    'email' => fn() => $this->user->email,
    'role' => fn() => $this->user->role,
    'active' => fn() => $this->user->active,
    'password' => '',
    'password_confirmation' => '',
]);

$roles = computed(function () {
   return Role::all();
});

$update = function () {
    $validated = $this->validate();

    if ($validated['password']) {
       $this->user->password = Hash::make($validated['password']);
    }

    if ($this->user->email != $validated['email']) {
        $this->user->email_verified_at = null;
        $this->user->sendEmailVerificationNotification();
    }

    $this->user->name = $validated['name'];
    $this->user->email = $validated['email'];
    $this->user->active = $validated['active'];

    if($this->user->mainRole && $this->user->mainRole->name != $validated['role']) {
        $this->user->syncRoles($validated['role']);
    }

    $this->user->save();

    $this->redirect(route('users', absolute: false), navigate: true);
};

mount(function (User $user) {
    $this->user = $user;
});


?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Edit User') }}
    </h2>
</x-slot>
<div class="pt-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <form wire:submit="update" class="max-w-xl">
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input  wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                autocomplete="new-password" />

                 <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <div class="flex gap-6 mt-4">
                <div class="flex-1">
                    <x-input-label for="role" :value="__('Role')" />
                    <select
                        wire:model="role"
                        name="role"
                        id="role"
                        class="form-control"
                    >
                        @foreach($this->roles as $itemRole)
                            <option
                                value="{{ $itemRole->name }}"
                                @selected($role === $itemRole->name)
                            >
                                {{ $itemRole->display_name }}
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
                        name="active"
                        id="active"
                        {{$active ? 'checked': ''}}
                    />
                </div>
            </div>
            <div class="flex items-center mt-6">
                <x-primary-button class="ms-4">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </div>
