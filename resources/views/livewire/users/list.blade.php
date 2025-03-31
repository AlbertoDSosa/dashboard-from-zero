<?php

use function Livewire\Volt\{state, with, usesPagination};
use function Livewire\Volt\layout;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

usesPagination();

state([
    'authUser' => fn() => Auth::user()
])->locked();

layout('layouts.app');

with(fn () => ['users' => User::paginate(10)]);

$delete = function ($userUuid) {

    if($this->authUser->cannot('delete users')) {
        abort(400);
    }

    $user = User::where('uuid', $userUuid)->first();

    if(!$user) {
        abort(400);
    }

    $user->delete();
};

?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Users') }}
    </h2>
</x-slot>

<div class="p-6">
    <div class="space-y-6 bg-white shadow sm:rounded-lg">
        <div class="flex justify-between items-center border-b-2 border-neutral-100 px-6 py-3 dark:border-white/10">
            <a
                type="button"
                href="{{route('users.create')}}"
                wire:navigate
                class="inline-block rounded bg-neutral-800 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-neutral-50 shadow-dark-3 transition duration-150 ease-in-out hover:bg-neutral-700 hover:shadow-dark-2 focus:bg-neutral-700 focus:shadow-dark-2 focus:outline-none focus:ring-0 active:bg-neutral-900 active:shadow-dark-2 motion-reduce:transition-none dark:shadow-black/30 dark:hover:shadow-dark-strong dark:focus:shadow-dark-strong dark:active:shadow-dark-strong">
                Create User
            </a>
            <div class="flex">
                <input
                  type="search"
                  class="relative m-0 block flex-auto rounded border border-solid border-neutral-200 bg-transparent bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-surface outline-none transition duration-200 ease-in-out placeholder:text-neutral-500 focus:z-[3] focus:border-primary focus:shadow-inset focus:outline-none motion-reduce:transition-none dark:border-white/10 dark:text-white dark:placeholder:text-neutral-200 dark:autofill:shadow-autofill dark:focus:border-primary"
                  placeholder="Search"
                  aria-label="Search"
                  id="exampleFormControlInput2"
                  aria-describedby="button-addon2" />
                <span
                  class="flex items-center whitespace-nowrap px-3 py-[0.25rem] text-surface dark:border-neutral-400 dark:text-white [&>svg]:h-5 [&>svg]:w-5"
                  id="button-addon2">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                  </svg>
                </span>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                  <table
                    class="min-w-full text-left text-sm font-light text-surface dark:text-white">
                    <thead
                      class="border-b border-neutral-200 font-medium dark:border-white/10">
                      <tr>
                        @can('show user ids')
                        <th scope="col" class="px-6 py-4">#</th>
                        @endcan
                        <th scope="col" class="px-6 py-4">Name</th>
                        <th scope="col" class="px-6 py-4">Email</th>
                        <th scope="col" class="px-6 py-4">Active</th>
                        <th scope="col" class="px-6 py-4">Role</th>
                        <th scope="col" class="px-6 py-4">Verified</th>
                        <th scope="col" class="px-6 py-4">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($users as $user)
                      <tr class="border-b border-neutral-200 dark:border-white/10">
                        @can('show user ids')
                        <td class="whitespace-nowrap px-6 py-4 font-medium">{{$user->id}}</td>
                        @endcan
                        <td class="whitespace-nowrap px-6 py-4">{{$user->name}}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{$user->email}}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{$user->active ? 'Sí' : 'No'}}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{$user->mainRole ? $user->mainRole->display_name : 'No Role'}}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{$user->email_verified_at}}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                          <div class="flex gap-4">
                            <a href="{{route('users.edit', ['user' => $user])}}" class="cursor-pointer" type="button">
                              <svg class="w-[24px] h-[24px] fill-[#8e8e8e]" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">

                                <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"></path>

                              </svg>
                            </a>
                            @can('delete users')
                            <button
                                wire:click="delete('{{$user->uuid}}')" class="cursor-pointer"
                                wire:confirm="Are you sure you want to delete this user?"
                            >
                              <svg class="w-[24px] h-[24px] fill-[#8e8e8e]" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">

                                <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0H284.2c12.1 0 23.2 6.8 28.6 17.7L320 32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32h96l7.2-14.3zM32 128H416V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V128zm96 64c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16z"></path>

                              </svg>
                            </button>
                            @endcan
                          </div>
                        </td>
                      </tr>
                      @endforeach

                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <x-table-footer :data="$users" />
        </div>
    </div>
</div>
