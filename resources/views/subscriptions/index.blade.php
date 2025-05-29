{{-- resources/views/subscriptions/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pilih Paket Berlangganan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 border border-green-400 p-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 border border-red-400 p-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="mb-4 font-medium text-sm text-blue-600 bg-blue-100 border border-blue-400 p-3 rounded">
                            {{ session('info') }}
                        </div>
                    @endif

                    @if (isset($activePlanDetails) && $activePlanDetails->isNotEmpty())
                        <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded">
                            <h3 class="text-lg font-semibold text-blue-700">Paket Aktif Anda Saat Ini:</h3>
                            <ul>
                                @foreach($activePlanDetails as $planId => $details)
                                    <li class="text-sm text-blue-600">
                                        <strong>{{ $details['name'] }}</strong> (Rank: {{ $details['rank'] }})
                                        @if ($details['ends_at'])
                                            - Akan berakhir pada: {{ \Carbon\Carbon::parse($details['ends_at'])->format('d M Y, H:i') }}
                                        @else
                                            - Aktif Selamanya
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-yellow-100 border border-yellow-300 rounded">
                            <h3 class="text-lg font-semibold text-yellow-700">Anda belum memiliki paket aktif.</h3>
                        </div>
                    @endif

                   {{-- resources/views/subscriptions/index.blade.php --}}
{{-- ... bagian atas view tetap sama ... --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($plans as $plan)
                            @php
                                $isThisPlanCurrentlyActive = isset($activePlanDetails[$plan->id]);
                                $buttonText = $isThisPlanCurrentlyActive ? "Perpanjang " . Str::title($plan->name) : "Pilih Paket " . Str::title($plan->name);
                                $buttonClass = $isThisPlanCurrentlyActive ? "bg-blue-600 hover:bg-blue-700" : "bg-indigo-600 hover:bg-indigo-700";
                            @endphp

                            <div class="border p-6 rounded-lg shadow-md flex flex-col justify-between
                                @if($isThisPlanCurrentlyActive) bg-indigo-50 border-indigo-300 @else bg-gray-50 @endif">
                                <div>
                                    <h3 class="text-2xl font-bold mb-2">{{ Str::title($plan->name) }} (Rank: {{$plan->rank}})</h3>
                                    <p class="text-gray-600 mb-1">{{ $plan->description }}</p>
                                    <p class="text-3xl font-extrabold mb-3">
                                        Rp {{ number_format($plan->price, 0, ',', '.') }}
                                        @if ($plan->duration_days)
                                            <span class="text-sm font-normal">/ {{ $plan->duration_days }} hari</span>
                                        @else
                                            <span class="text-sm font-normal">/ Selamanya</span>
                                        @endif
                                    </p>
                                    <ul class="mb-4 text-sm text-gray-500">
                                        @if ($plan->slug == 'gold')
                                            <li>✔️ Fitur dasar Gold</li>
                                        @elseif ($plan->slug == 'platinum')
                                            <li>✔️ Semua fitur Gold</li>
                                            <li>✔️ Akses fitur Platinum</li>
                                        @elseif ($plan->slug == 'diamond')
                                            <li>✔️ Semua fitur Platinum</li>
                                            <li>✔️ Akses fitur Diamond</li>
                                            <li>✔️ Dukungan prioritas</li>
                                        @endif
                                        <li>✔️ Pembayaran via Tripay</li>
                                    </ul>
                                </div>

                                {{-- MODIFIKASI DI SINI: Ubah form menjadi link ke halaman checkout --}}
                                <div class="mt-4">
    <a href="{{ route('subscription.checkout', ['plan_slug' => $plan->slug]) }}"  {{-- Ganti plan_id menjadi plan_slug --}}
       class="block text-center w-full {{ $buttonClass }} text-white font-bold py-2 px-4 rounded transition-colors duration-150">
        {{ $buttonText }}
    </a>
</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>