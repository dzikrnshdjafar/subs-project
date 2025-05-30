{{-- resources/views/partials/services.blade.php --}}
<div class="max-w-7xl mx-auto px-4 text-center">
    <div class="py-16">
        
        <h2 class="text-4xl md:text-5xl font-bold mb-4 text-black">
            Temukan <span class="bg-orange px-2 text-white shadow-neu-sm">Layanan Premium</span> yang Kami Tawarkan
        </h2>
        <p class="text-lg md:text-xl text-gray-700 mb-12 max-w-3xl mx-auto">
            Dari hiburan hingga produktivitas, kami memberikan akses ke lebih dari 60 layanan dan aplikasi premium dengan satu langganan hemat.
        </p>
        
        @php
        // Data layanan (contoh, Anda perlu mengganti ini dengan data Anda)
        // Idealnya, ini datang dari database atau config file
        $services = [
            ['name' => 'ChatGPT', 'icon_url' => 'https://cdn-icons-png.flaticon.com/128/12222/12222588.png', 'bg_color' => 'bg-pink', 'text_color' => 'text-white'],
            ['name' => 'Netflix', 'icon_url' => 'https://cdn-icons-png.flaticon.com/128/732/732228.png', 'bg_color' => 'bg-red-600', 'text_color' => 'text-white'],
            ['name' => 'Disney+', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/0063E5?text=D%2B&font=roboto', 'bg_color' => 'bg-blue-700', 'text_color' => 'text-white'],
            ['name' => 'Canva', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/00C4CC?text=Ca&font=roboto', 'bg_color' => 'bg-teal-500', 'text_color' => 'text-white'],
            ['name' => 'Vidio', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/7B1FA2?text=V&font=roboto', 'bg_color' => 'bg-purple-600', 'text_color' => 'text-white'],
            ['name' => 'Prime Video', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/00A8E1?text=PV&font=roboto', 'bg_color' => 'bg-sky-500', 'text_color' => 'text-white'],
            ['name' => 'Freepik', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/0F7360?text=F&font=roboto', 'bg_color' => 'bg-emerald-600', 'text_color' => 'text-white'],
            ['name' => 'Envato Elements', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/82B540?text=Ee&font=roboto', 'bg_color' => 'bg-lime-600', 'text_color' => 'text-white'],
            ['name' => 'CapCut', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/000000?text=CC&font=roboto', 'bg_color' => 'bg-black', 'text_color' => 'text-white'],
            ['name' => 'Viu', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/FF6600?text=Viu&font=roboto', 'bg_color' => 'bg-orange', 'text_color' => 'text-white'],
            // Tambahkan lebih banyak layanan di sini
            ['name' => 'Semrush', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/FF6000?text=S&font=roboto', 'bg_color' => 'bg-orange-500', 'text_color' => 'text-white'],
            ['name' => 'DeepL', 'icon_url' => 'https://placehold.co/64x64/FFFAE3/0F2B46?text=DL&font=roboto', 'bg_color' => 'bg-blue-900', 'text_color' => 'text-white'],
        ];
        @endphp

<div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-10 gap-y-8">
    @foreach($services as $service)
    <div class="flex flex-col items-center group">
        <div class="{{ $service['bg_color'] ?? 'bg-gray-200' }} p-2 border-3 border-black shadow-neu group-hover:shadow-neu-lg group-hover:-translate-y-1 group-hover:translate-x-1 transition-all duration-150 ease-in-out rounded-md xl:w-24 xl:h-24 md:w-20 md:h-20 flex items-center justify-center">
            <img src="{{ $service['icon_url'] }}" alt="{{ $service['name'] }} logo" class="w-8 h-8 md:w-20 md:h-20 object-contain" 
            onerror="this.onerror=null; this.src='https://placehold.co/64x64/CCCCCC/969696?text=Error&font=roboto';">
        </div>
        <p class="mt-2 text-xs md:text-sm font-medium text-black truncate w-16 md:w-20 text-center">{{ $service['name'] }}</p>
    </div>
    @endforeach
</div>
</div>
</div>