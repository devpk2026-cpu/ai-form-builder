<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between h-16">

            {{-- Left Side --}}
            <div class="flex">

                {{-- Logo / Brand --}}
                <div class="shrink-0 flex items-center">

                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">

                        <div class="w-9 h-9 bg-black rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-lg">
                                AI
                            </span>
                        </div>

                        <div class="hidden sm:block">
                            <span class="font-bold text-lg text-gray-900">
                                AI Form Builder
                            </span>
                        </div>

                    </a>

                </div>


                {{-- Desktop Navigation --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    {{-- Dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        Dashboard
                    </x-nav-link>


                    {{-- My Forms --}}
                    <x-nav-link :href="route('forms.index')" :active="request()->routeIs('forms.index')" wire:navigate>
                        My Forms
                    </x-nav-link>

                    <x-nav-link :href="route('imports.create')" :active="request()->routeIs('imports.*')" wire:navigate>
                        {{ __('Import Form') }}
                    </x-nav-link>


                    {{-- Create Form --}}
                    <x-nav-link :href="route('forms.create')" :active="request()->routeIs('forms.create')" wire:navigate>
                        Create Form
                    </x-nav-link>

                </div>

            </div>


            {{-- Right Side --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">

                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-900 focus:outline-none transition">

                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-2">

                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 010 1.414l-4 4a1 1 0 010-1.414l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>

                            </div>

                        </button>

                    </x-slot>


                    <x-slot name="content">

                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            Profile
                        </x-dropdown-link>


                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                Log Out
                            </x-dropdown-link>
                        </button>

                    </x-slot>

                </x-dropdown>

            </div>


            {{-- Mobile Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">

                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">

                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">

                        <path
                            :class="{
                                'hidden': open,
                                'inline-flex': !open
                            }"
                            class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path
                            :class="{
                                'hidden': !open,
                                'inline-flex': open
                            }"
                            class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

            </div>

        </div>

    </div>


    {{-- Mobile Navigation --}}
    <div :class="{
        'block': open,
        'hidden': !open
    }"
        class="hidden sm:hidden border-t border-gray-100">

        <div class="pt-2 pb-3 space-y-1">

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                Dashboard
            </x-responsive-nav-link>


            <x-responsive-nav-link :href="route('forms.index')" :active="request()->routeIs('forms.index')" wire:navigate>
                My Forms
            </x-responsive-nav-link>


            <x-responsive-nav-link :href="route('forms.create')" :active="request()->routeIs('forms.create')" wire:navigate>
                Create Form
            </x-responsive-nav-link>

        </div>


        {{-- Mobile User --}}
        <div class="pt-4 pb-1 border-t border-gray-200">

            <div class="px-4">

                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"></div>

                <div class="font-medium text-sm text-gray-500">
                    {{ auth()->user()->email }}
                </div>

            </div>


            <div class="mt-3 space-y-1">

                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    Profile
                </x-responsive-nav-link>


                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        Log Out
                    </x-responsive-nav-link>
                </button>

            </div>

        </div>

    </div>

</nav>
