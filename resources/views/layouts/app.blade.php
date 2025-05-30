{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Font diimpor via app.css --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-cream text-black">
        <div class="min-h-screen flex flex-col"> {{-- Added flex flex-col --}}
            
            @include('layouts.navigation')

            <main class="flex-grow "> {{-- Added flex-grow --}}
                {{ $slot }}
            </main>

            @include('layouts.footer')
        </div>
    </body>
</html>