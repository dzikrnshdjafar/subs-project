{{-- resources/views/layouts/navigation.blade.php --}}
<nav x-data="{ open: false }" class="px-6 py-4 md:px-12 flex justify-between items-center border-b-3 border-black bg-cream">
    <div class="flex items-center">
        <a href="{{ route('home') }}">
            <div class="bg-pink p-2 border-3 border-black shadow-neu">
                <span class="font-bold text-xl">{{ config('app.name', 'NEOBRU') }}</span>
            </div>
        </a>
    </div>

    <div class="hidden md:flex space-x-6 items-center">
        <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="font-bold hover:underline text-black">
            {{ __('Beranda') }}
        </x-nav-link>

        {{-- Link Paket Langganan yang Disesuaikan --}}
        @if(request()->routeIs('home'))
    <a href="#subscription-plans-section"
       class="font-bold hover:underline text-black px-1 pt-1 border-b-2 border-transparent hover:border-gray-400 focus:outline-none focus:border-orange transition duration-150 ease-in-out">
        {{ __('Paket Langganan') }}
    </a>
@else
    <a href="{{ route('home') }}#subscription-plans-section"
       class="font-bold hover:underline text-black px-1 pt-1 border-b-2 border-transparent hover:border-gray-400 focus:outline-none focus:border-orange transition duration-150 ease-in-out">
        {{ __('Paket Langganan') }}
    </a>
@endif
        {{-- End Link Paket Langganan yang Disesuaikan --}}


        @auth
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-bold hover:underline text-black">
                {{ __('Dashboard') }}
            </x-nav-link>
        @endauth
    </div>

    <div class="hidden sm:flex sm:items-center sm:ms-6">
        @auth
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="bg-orange px-5 py-2 font-bold text-black border-3 border-black shadow-neu transition-all hover:-translate-y-1 hover:translate-x-1 hover:shadow-neu-lg active:translate-y-1 active:-translate-x-1 active:shadow-neu-sm flex items-center rounded-md">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <div class="py-1 bg-white border-3 border-black shadow-neu-sm rounded-md">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.activity-log')">
                            {{ __('Log Aktivitas') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </div>
                </x-slot>
            </x-dropdown>
        @else
             <div class="space-x-3">
                <a href="{{ route('login') }}">
                    <button class="bg-orange px-5 py-2 font-bold text-black border-3 border-black shadow-neu transition-all hover:-translate-y-1 hover:translate-x-1 hover:shadow-neu-lg active:translate-y-1 active:-translate-x-1 active:shadow-neu-sm rounded-md">
                        Log in
                    </button>
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">
                        <button class="bg-yellow px-5 py-2 font-bold text-black border-3 border-black shadow-neu transition-all hover:-translate-y-1 hover:translate-x-1 hover:shadow-neu-lg active:translate-y-1 active:-translate-x-1 active:shadow-neu-sm rounded-md">

                            Register
                        </button>
                    </a>
                @endif
            </div>
        @endauth
    </div>

    <div class="-me-2 flex items-center sm:hidden">
        <button @click="open = ! open" class="bg-cream p-2 rounded-md text-black focus:outline-none border-3 border-black shadow-neu active:shadow-none">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden fixed inset-x-0 top-[calc(theme(spacing.16)+3px)] z-40 bg-cream border-t-3 border-l-3 border-r-3 border-b-3 border-black shadow-neu-lg rounded-b-lg mx-4">
        {{-- Menyesuaikan top-[calc(theme(spacing.16)+3px)] agar tepat di bawah nav bar (h-16 + border-b-3) --}}
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Beranda') }}
            </x-responsive-nav-link>

            {{-- Link Paket Langganan Responsif --}}
@if(request()->routeIs('home'))
    {{-- Untuk halaman home (scroll internal) --}}
    <a href="#subscription-plans-section"
       class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-black hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out
              md:font-bold md:hover:underline md:text-black md:px-1 md:pt-1 md:border-b-2 md:border-transparent md:hover:border-gray-400 md:focus:border-orange">
        {{ __('Paket Langganan') }}
    </a>
@else
    {{-- Untuk halaman lain (redirect ke home dengan anchor) --}}
    <a href="{{ route('home') }}#subscription-plans-section"
       class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-black hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out
              md:font-bold md:hover:underline md:text-black md:px-1 md:pt-1 md:border-b-2 md:border-transparent md:hover:border-gray-400 md:focus:border-orange">
        {{ __('Paket Langganan') }}
    </a>
@endif
{{-- End Link Paket Langganan Responsif --}}

            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        {{-- Responsive Settings Options --}}
        @auth
            <div class="pt-4 pb-3 border-t border-gray-400">
                <div class="px-4">
                    <div class="font-bold text-base text-black">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-700">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.activity-log')">
                        {{ __('Log Aktivitas') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-3 border-t border-gray-400">
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Log In') }}
                    </x-responsive-nav-link>
                    @if (Route::has('register'))
                        <x-responsive-nav-link :href="route('register')">
                            {{ __('Register') }}
                        </x-responsive-nav-link>
                    @endif
                </div>
            </div>
        @endauth
    </div>
</nav>