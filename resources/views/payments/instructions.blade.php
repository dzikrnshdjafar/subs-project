<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Instruksi Pembayaran - {{ $payment->plan->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 border border-green-400 p-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h3 class="text-2xl font-semibold mb-4">Segera Lakukan Pembayaran</h3>
                    <p class="mb-2">Paket: <strong>{{ $payment->plan->name }}</strong></p>
                    <p class="mb-2">Jumlah Tagihan: <strong class="text-lg text-indigo-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></p>
                    <p class="mb-2">Metode Pembayaran: <strong>{{ $payment->payment_method_name }}</strong></p>

                    @if($payment->pay_code)
                        <p class="mb-4">Kode Bayar/Nomor Virtual Account: <strong class="text-xl bg-gray-100 px-2 py-1 rounded">{{ $payment->pay_code }}</strong></p>
                    @endif

                    @if($payment->expired_time)
                        <p class="mb-6 text-red-600">Batas Waktu Pembayaran: <strong>{{ $payment->expired_time->format('d M Y, H:i:s') }} (WIB)</strong></p>
                    @endif

                    @if($payment->instructions && count($payment->instructions) > 0)
                        <div class="mt-6 border-t pt-6">
                            <h4 class="text-xl font-semibold mb-3">Instruksi Pembayaran:</h4>
                            @foreach($payment->instructions as $instruction)
                                <div class="mb-4 p-4 border rounded-md bg-gray-50">
                                    <h5 class="font-semibold text-lg mb-2">{{ $instruction['title'] }}</h5>
                                    <ol class="list-decimal list-inside space-y-1 text-sm">
                                        @foreach($instruction['steps'] as $step)
                                            <li>{!! nl2br(e($step)) !!}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">Silakan lakukan pembayaran sesuai dengan metode yang Anda pilih.</p>
                    @endif

                    <div class="mt-8 text-center">
                         <a href="{{ route('subscription.plans') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Kembali ke Pilihan Paket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>