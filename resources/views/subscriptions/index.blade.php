    <div class="max-w-7xl mx-auto px-6">
    <div class="py-16">

            @auth {{-- Tampilkan hanya jika user login --}}
                @if (isset($activePlanDetails) && $activePlanDetails->isNotEmpty())
                    <div class="mb-6 p-6 bg-blue border-3 border-black shadow-neu-lg rounded-lg">
                        <h3 class="text-2xl font-bold text-black mb-3">Paket Aktif Anda Saat Ini:</h3>
                        <ul class="list-disc list-inside ml-4">
                            @foreach($activePlanDetails as $planId => $details)
                                <li class="text-black mb-1">
                                    <strong class="font-semibold">{{ Str::title($details['name']) }}</strong> (Rank: {{ $details['rank'] }})
                                    @if ($details['ends_at'])
                                        - Akan berakhir pada: <span class="font-medium">{{ \Carbon\Carbon::parse($details['ends_at'])->format('d M Y, H:i') }}</span>
                                    @else
                                        - <span class="font-medium">Aktif Selamanya</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mb-6 p-6 bg-yellow border-3 border-black shadow-neu-lg rounded-lg">
                        <h3 class="text-2xl font-bold text-black">Anda belum memiliki paket aktif.</h3>
                        <p class="text-black mt-2">Silakan pilih salah satu paket di bawah ini untuk memulai.</p>
                    </div>
                @endif
            @endauth

            <h2 class="text-4xl md:text-5xl font-bold mb-12 text-center">Choose <span class="bg-orange px-2">Your Plan</span></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($plans as $plan)
                    @php
                        $isThisPlanCurrentlyActive = false; // Default untuk tamu
                        if(Auth::check()){ // Cek hanya jika user login
                           $isThisPlanCurrentlyActive = isset($activePlanDetails[$plan->id]);
                        }
                        $buttonText = $isThisPlanCurrentlyActive ? "Perpanjang " . Str::title($plan->name) : "Pilih Paket " . Str::title($plan->name);
                        
                        $buttonBaseClasses = "block text-center w-full text-black font-bold py-3 px-4 border-3 border-black shadow-neu transition-all hover:-translate-y-1 hover:translate-x-1 hover:shadow-neu-lg active:translate-y-1 active:-translate-x-1 active:shadow-neu-sm rounded-md";
                        $buttonColorClass = "bg-white hover:bg-white"; // Default warna untuk "Pilih Paket"
                        
                        // Jika user login dan paket ini aktif, tombol mungkin bisa beda warna
                        if ($isThisPlanCurrentlyActive && Auth::check()) {
                             $buttonColorClass = "bg-cream hover:bg-cream"; // Contoh: Biru untuk perpanjang
                        }

                        $cardBgColor = 'bg-gray-100';
                        $iconSvg = '';
                        if ($plan->slug == 'gold') {
                            $cardBgColor = 'bg-yellow';
                            $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" stroke="black" fill="#FFD700"/><path d="M6 12h12" stroke="black"/><path d="M8 7l8 0" stroke="black"/><path d="M8 17l8 0" stroke="black"/></svg>';
                        } elseif ($plan->slug == 'platinum') {
                            $cardBgColor = 'bg-pink';
                            $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="12" rx="2" stroke="black" fill="#FF577F"/><line x1="3" y1="10" x2="21" y2="10" stroke="black"/><line x1="7" y1="15" x2="17" y2="15" stroke="black"/></svg>';
                        } elseif ($plan->slug == 'diamond') {
                            $cardBgColor = 'bg-teal';
                            $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 5 10 12 18 19 10" stroke="black" fill="#B9F2FF"/><line x1="12" y1="2" x2="12" y2="18" stroke="black" stroke-opacity="0.3"/><line x1="5" y1="10" x2="19" y2="10" stroke="black" stroke-opacity="0.3"/></svg>';
                        }
                    @endphp

                    <div class="{{ $cardBgColor }} p-6 border-3 border-black shadow-neu-lg rounded-lg flex flex-col justify-between">
                        <div>
                            <div class="bg-white p-4 mb-4 inline-block border-3 border-black shadow-neu rounded-md">
                                {!! $iconSvg !!}
                            </div>
                            <h3 class="text-3xl font-bold mb-2 text-black">{{ Str::title($plan->name) }}</h3>
                            <p class="text-black mb-1">{{ $plan->description }}</p>
                            <p class="text-3xl font-extrabold mb-3 text-black">
                                Rp {{ number_format($plan->price, 0, ',', '.') }}
                                @if ($plan->duration_days)
                                    <span class="text-base font-normal">/ {{ $plan->duration_days }} hari</span>
                                @else
                                    <span class="text-base font-normal">/ Selamanya</span>
                                @endif
                            </p>
                            <ul class="mb-4 space-y-1 text-black text-xl">
                                @if ($plan->slug == 'gold')
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Fitur dasar Gold
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Dukungan Komunitas
                                    </li>
                                @elseif ($plan->slug == 'platinum')
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Semua fitur Gold
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Akses fitur Platinum
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Dukungan Email
                                    </li>
                                @elseif ($plan->slug == 'diamond')
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Semua fitur Platinum
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Akses fitur Diamond
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Dukungan Prioritas (Telepon & Email)
                                    </li>
                                @endif
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Pembayaran via Tripay
                                </li>
                            </ul>
                        </div>
                        <div class="mt-auto pt-4">
                            <a href="{{ route('subscription.checkout', ['plan_slug' => $plan->slug]) }}"
                               class="{{ $buttonBaseClasses }} {{ $buttonColorClass }}">
                                {{ $buttonText }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        </div>