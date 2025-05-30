{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- The font is now imported in app.css --}}

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-cream"> {{-- Changed bg-gray-100 to bg-cream --}}
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-6">
            <div>
                <a href="/">
                    {{-- Neubrutalist Logo attempt --}}
                    <div class="bg-pink p-2 border-3 border-black shadow-neu mb-4">
                        <span class="font-bold text-2xl">{{ config('app.name', 'Laravel') }}</span> {{-- Or your app name/logo text --}}
                    </div>
                    {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-neu-lg border-3 border-black overflow-hidden sm:rounded-lg"> {{-- Added Neubrutalist shadow and border --}}
                {{ $slot }}
            </div>

            {{-- Simplified Footer for Guest Pages --}}
            <footer class="py-8 text-center">
                <p class="text-sm text-gray-700">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            </footer>
        </div>
    </body>
</html>