{{-- resources/views/home.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Beranda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral border-3 border-black shadow-neu-lg overflow-hidden rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-3xl font-bold mb-4">Selamat Datang di {{ config('app.name', 'Laravel') }}!</h1>
                    <p class="text-lg">Ini adalah halaman beranda publik yang menggunakan layout Neubrutalism.</p>
                    <p class="mt-4">Anda dapat menelusuri <a href="{{ route('subscription.plans') }}" class="text-orange hover:underline font-semibold">paket langganan kami</a> atau melihat fitur-fitur lainnya.</p>

                    @guest
                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}">
                            <button class="bg-orange px-6 py-3 font-bold text-black border-3 border-black shadow-neu transition-all hover:-translate-y-1 hover:translate-x-1 hover:shadow-neu-lg active:translate-y-1 active:-translate-x-1 active:shadow-neu-sm rounded-md">
                                Dashboard
                            </button>
                        </a>
                        
                    </div>
                    @endguest
                </div>
            </div>

            {{-- Bagian Paket Langganan --}}
            <section id="subscription-plans-section" class="mb-16">
               @include('subscriptions.index')
            </section>

            {{-- Anda bisa menambahkan konten dari neubrutalism.html di sini jika diinginkan --}}
            {{-- Misalnya, bagian Features atau CTA --}}
            <div class="mt-16">
                {{-- Contoh Integrasi Bagian "Features" dari neubrutalism.html --}}
                <section class="px-0"> {{-- px-0 karena max-w-7xl sudah ada di atas --}}
                    <h2 class="text-4xl md:text-5xl font-bold mb-12 text-center">Our <span class="bg-yellow px-2">Features</span></h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-teal p-6 border-3 border-black shadow-neu-lg rounded-lg">
                            <div class="bg-white p-4 mb-4 inline-block border-3 border-black shadow-neu rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-2 text-black">Customizable</h3>
                            <p class="mb-4 text-black">Easily customize every aspect of your design with our powerful tools.</p>
                            <a href="#" class="font-bold underline text-black">Learn more →</a>
                        </div>
                        <div class="bg-pink p-6 border-3 border-black shadow-neu-lg rounded-lg">
                            <div class="bg-white p-4 mb-4 inline-block border-3 border-black shadow-neu rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-2 text-black">Lightning Fast</h3>
                            <p class="mb-4 text-black">Our optimized code ensures your website loads quickly every time.</p>
                            <a href="#" class="font-bold underline text-black">Learn more →</a>
                        </div>
                        <div class="bg-yellow p-6 border-3 border-black shadow-neu-lg rounded-lg">
                            <div class="bg-white p-4 mb-4 inline-block border-3 border-black shadow-neu rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-2 text-black">Secure</h3>
                            <p class="mb-4 text-black">Built with security in mind to keep your data safe and protected.</p>
                            <a href="#" class="font-bold underline text-black">Learn more →</a>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </div>

</x-app-layout>