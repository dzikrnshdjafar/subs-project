<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Status Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    @if($status === 'success')
                        <div class="text-green-500 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-green-700 mb-2">Pembayaran Berhasil!</h3>
                    @elseif($status === 'failed')
                        <div class="text-red-500 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-red-700 mb-2">Pembayaran Gagal!</h3>
                    @else
                        <div class="text-yellow-500 mb-4">
                             <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.79 4 4s-1.79 4-4 4S8 12.21 8 10c0-.313.079-.613.228-.886M12 20c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.799 2.96C5.077 19.213 5 19.612 5 20c0 .388.077.787.228 1.168.15.381.383.75.68 1.111.297.362.638.714.998 1.056a4.99 4.99 0 004.094 1.665c.398 0 .797-.078 1.183-.229.385-.15.754-.382 1.112-.68.357-.297.709-.637 1.056-.997.381-.328.708-.68.997-1.055.167-.247.32-.508.457-.781A5.005 5.005 0 0012 20z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-yellow-700 mb-2">Pembayaran Diproses</h3>
                    @endif
                    <p class="text-gray-600 mb-6">{{ $message }}</p>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Kembali ke Dashboard
                    </a>
                     <a href="{{ route('subscription.plans') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Lihat Paket Lain
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>