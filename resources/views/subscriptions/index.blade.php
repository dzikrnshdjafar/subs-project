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

                    @if ($currentPlan)
                        <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded">
                            <h3 class="text-lg font-semibold text-blue-700">Paket Anda Saat Ini: {{ $currentPlan->name }}</h3>
                            @if (Auth::user()->activeSubscription && Auth::user()->activeSubscription->first() && Auth::user()->activeSubscription->first()->ends_at)
                                <p class="text-sm text-blue-600">
                                    Akan berakhir pada: {{ Auth::user()->activeSubscription->first()->ends_at->format('d M Y, H:i') }}
                                </p>
                            @elseif ($currentPlan->slug !== 'free')
                                 <p class="text-sm text-blue-600">Status: Aktif</p>
                            @endif
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($plans as $plan)
                            <div class="border p-6 rounded-lg shadow-md @if($currentPlan && $currentPlan->id === $plan->id) bg-indigo-50 border-indigo-300 @endif">
                                <h3 class="text-2xl font-bold mb-2">{{ $plan->name }}</h3>
                                <p class="text-gray-600 mb-1">{{ $plan->description }}</p>
                                <p class="text-3xl font-extrabold mb-3">
                                    ${{ number_format($plan->price, 0) }}
                                    @if ($plan->duration_days)
                                        <span class="text-sm font-normal">/ {{ $plan->duration_days }} hari</span>
                                    @else
                                        <span class="text-sm font-normal">/ Selamanya</span>
                                    @endif
                                </p>
                                <ul class="mb-4 text-sm text-gray-500">
                                    @if ($plan->slug == 'free')
                                        <li>✔️ Fitur dasar</li>
                                    @elseif ($plan->slug == 'basic')
                                        <li>✔️ Semua fitur free</li>
                                        <li>✔️ Akses fitur basic</li>
                                        <li>✔️ Durasi 10 hari</li>
                                    @elseif ($plan->slug == 'premium')
                                        <li>✔️ Semua fitur basic</li>
                                        <li>✔️ Akses fitur premium</li>
                                        <li>✔️ Durasi 30 hari</li>
                                        <li>✔️ Dukungan prioritas</li>
                                    @endif
                                </ul>

                                @if ($currentPlan && $currentPlan->id === $plan->id && (is_null(Auth::user()->activeSubscription->first()->ends_at) || Auth::user()->activeSubscription->first()->ends_at > now()))
                                     <button class="w-full bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed" disabled>
                                        Paket Aktif
                                    </button>
                                @elseif ($plan->slug === 'free' && $currentPlan && $currentPlan->slug !== 'free')
                                    {{-- Opsi downgrade ke free mungkin perlu logika khusus atau tidak diizinkan --}}
                                     <button class="w-full bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded cursor-not-allowed" disabled>
                                        Tidak Tersedia
                                    </button>
                                @else
                                    <form action="{{ route('subscription.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                            Pilih Paket {{ $plan->name }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>