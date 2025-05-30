{{-- resources/views/home.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Beranda') }}
        </h2>
    </x-slot>

    <section id="hero" class="bg-cream overflow-hidden rounded-lg py-4 md:py-10">
    @include('hero')
    </section>
    
        {{-- Bagian Fitur --}}
        <section id="features-section" class="py-16 bg-neutral">
            
                @include('features')
            </section>
            
            {{-- Bagian Paket Langganan --}}
            <section id="subscription-plans-section" class="py-16 bg-cream">
                @include('subscriptions.index')
            </section>

            {{-- Bagian Layanan Premium Baru --}}
    <section id="premium-services-section" class="py-16 bg-blue"> {{-- Menggunakan bg-blue sebagai contoh, sesuaikan --}}
        @include('services') {{-- Atau 'services' jika Anda tidak meletakkannya di subfolder partials --}}
    </section>
            

</x-app-layout>