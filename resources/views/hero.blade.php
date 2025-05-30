<div class="max-w-7xl mx-auto px-4 xl:py-40 md:py-32 sm:py-4">
        <div class="p-6 text-neutral">
            <h1 class="text-7xl font-bold mb-4">Lorem ipsum dolor sit amet. {{ config('app.name', 'Laravel') }}!</h1>
            <p class="text-lg font-bold">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Debitis quam eos incidunt dolorum officiis cumque id blanditiis non earum nemo enim, illum, reprehenderit quae sunt praesentium ipsum magni accusantium sed molestiae vitae nam, exercitationem est. Porro tenetur fugit ipsam in.</p>
            <p class="mt-4">Anda dapat menelusuri <a href="#subscription-plans-section" class="text-orange hover:underline font-semibold">paket langganan kami</a> atau melihat fitur-fitur lainnya.</p>
            
            <div class="flex gap-4">
                
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}">
                        <button class="bg-lime px-6 py-3 font-bold text-black border-3 border-black shadow-neu transition-all hover:-translate-y-1 hover:translate-x-1 hover:shadow-neu-lg active:translate-y-1 active:-translate-x-1 active:shadow-neu-sm rounded-md">
                            Dashboard
                        </button>
                    </a>
                    
                </div>
                @guest
                <div class="mt-6">
                    <a href="https://www.youtube.com/" target="_blank">
    <button class="bg-cream px-6 py-3 font-bold text-black border-3 border-black shadow-neu transition-all hover:-translate-y-1 hover:translate-x-1 hover:shadow-neu-lg active:translate-y-1 active:-translate-x-1 active:shadow-neu-sm rounded-md flex items-center space-x-2">
        
        Lihat Demo
        <svg class="w-6 h-6 fill-current text-black" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M8 5v14l11-7z"></path>
        </svg>
    </button>
</a>

                    
                </div>
                @endguest
            </div>
        </div>
    </div>