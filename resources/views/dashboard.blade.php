{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Your Plan Section --}}
            <div class="bg-white text-primary p-6 border-3 border-black shadow-neu-lg rounded-lg">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wider">Your plan</h3>
                    {{-- Icon kanan atas (contoh: refresh atau settings) --}}
                    <a href="#" class="text-primary hover:text-yellow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m-15.357-2a8.001 8.001 0 0015.357 2.001M9 15h4.581" />
                        </svg>
                    </a>
                </div>
                @if($currentPlan)
                    <h2 class="text-5xl text-center font-bold mb-1">{{ Str::title($currentPlan->name) }}</h2>
                    <p class="text-sm text-gray-400 text-end">
                        @if($currentPlan->duration_days && $activePlanDetails->first()['ends_at'])
                            Expires on: {{ \Carbon\Carbon::parse($activePlanDetails->first()['ends_at'])->format('d M Y, H:i') }}
                        @elseif(!$currentPlan->duration_days)
                            Active indefinitely
                        @else
                            Premium benefits are active.
                        @endif
                    </p>
                @else
                    <h2 class="text-3xl font-bold mb-1">No Premium</h2>
                    <p class="text-sm text-gray-400">Premium are required to access Groupy Extension.</p>
                @endif
            </div>

            {{-- Account Section --}}
            <div class="bg-white text-primary p-4 border-3 border-black shadow-neu-lg rounded-lg">
                <h3 class="text-lg font-semibold mb-1 px-2">Account</h3>
                <ul>
                    <li>
                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-3 hover:bg-yellow rounded-md transition-colors duration-150 ease-in-out group">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                <span>Edit profile</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile.activity-log') }}" class="flex items-center justify-between p-3 hover:bg-yellow rounded-md transition-colors duration-150 ease-in-out group">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Activity logs</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center justify-between p-3 hover:bg-yellow rounded-md transition-colors duration-150 ease-in-out group">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"> <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /> </svg>
                                <span>Manage Discord connection</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Premium Section --}}
            <div class="bg-white text-primary p-4 border-3 border-black shadow-neu-lg rounded-lg">
                <h3 class="text-lg font-semibold mb-1 px-2">Premium</h3>
                <ul>
                    <li>
                        <a href="{{ route('subscription.plans') }}" class="flex items-center justify-between p-3 hover:bg-yellow rounded-md transition-colors duration-150 ease-in-out group">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                <span>Purchase premium</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile.activity-log') }}" class="flex items-center justify-between p-3 hover:bg-yellow rounded-md transition-colors duration-150 ease-in-out group">
                            <div class="flex items-center">
                               <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                <span>Order history</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Extension Section --}}
            <div class="bg-white text-primary p-4 border-3 border-black shadow-neu-lg rounded-lg">
                <h3 class="text-lg font-semibold mb-1 px-2">Extension</h3>
                <ul>
                    <li>
                        <a href="#" class="flex items-center justify-between p-3 hover:bg-yellow rounded-md transition-colors duration-150 ease-in-out group">
                            <div class="flex items-center">
                               <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                <span>Download Groupy Guard (version 1.0.2)</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center justify-between p-3 hover:bg-yellow rounded-md transition-colors duration-150 ease-in-out group">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                <span>Download Groupy Extension (version 2.1.2)</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center justify-between p-3 hover:bg-yellow rounded-md transition-colors duration-150 ease-in-out group">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Watch the installation video (Windows/macOS/Linux with Google Chrome)</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center justify-between p-3 hover:bg-yellow rounded-md transition-colors duration-150 ease-in-out group">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Watch the installation video (Android with Kiwi Browser)</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>