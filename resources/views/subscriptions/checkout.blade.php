{{-- resources/views/subscriptions/checkout.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Purchase Premium') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    @if (session('warning'))
                        <div class="mb-4 font-medium text-sm text-yellow-700 bg-yellow-100 border border-yellow-400 p-3 rounded">
                            {{ session('warning') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 border border-red-400 p-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('subscription.initiatePayment') }}" method="POST">
                        @csrf
                        {{-- Input hidden plan_id akan diisi oleh JavaScript atau bisa menggunakan name dari select --}}
                        {{-- <input type="hidden" name="plan_id" id="plan_id_hidden" value="{{ $selectedPlan->id }}"> --}}

                        <div class="mb-6">
                            <label for="plan_id_select" class="block text-sm font-medium text-gray-700">Plan</label>
                            <select name="plan_id" id="plan_id_select" required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @foreach($allAvailablePlans as $planOption)
                                    <option value="{{ $planOption->id }}" data-price="{{ $planOption->price }}" {{ $selectedPlan->id == $planOption->id ? 'selected' : '' }}>
                                        {{ Str::title($planOption->name) }} (Rp {{ number_format($planOption->price, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <p id="plan_price_display" class="mt-2 text-sm text-gray-600">
                                Harga: Rp <span id="dynamic_price">{{ number_format($selectedPlan->price, 0, ',', '.') }}</span>
                                @if ($selectedPlan->duration_days)
                                    / {{ $selectedPlan->duration_days }} hari
                                @else
                                    / Selamanya
                                @endif
                            </p>
                        </div>

                        <div class="mb-6">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment method</label>
                            @if(isset($paymentChannels) && !empty($paymentChannels))
                                <select name="payment_method" id="payment_method" required
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="" disabled selected>-- Select Payment Method --</option>
                                    @foreach ($paymentChannels as $channel)
                                        @if($channel['active'])
                                            <option value="{{ $channel['code'] }}" data-channel-info="{{ json_encode($channel) }}">
                                                {{ $channel['name'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <p id="payment_fee_display" class="mt-1 text-xs text-gray-500"></p>
                            @else
                                <p class="mt-1 text-sm text-red-600">Payment methods are currently unavailable.</p>
                            @endif
                        </div>

                        <div class="mb-6">
                            <label for="promotion_code" class="block text-sm font-medium text-gray-700">Promotion code</label>
                            <input type="text" name="promotion_code" id="promotion_code"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Enter promotion code (optional)">
                        </div>

                        <div class="mb-6 text-xs text-gray-500">
                            By completing your purchase, you agree with our <a href="#" class="underline hover:text-gray-700">Terms of Service</a>.
                        </div>

                        <div>
                            <button type="submit"
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                    @if(empty($paymentChannels)) disabled @endif>
                                Purchase
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 text-center text-sm">
                        <p class="text-gray-600">
                            Looking for your order history? <a href="{{ route('profile.activity-log') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Order history</a>
                        </p>
                        <p class="mt-2">
                            <a href="{{ route('dashboard') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Back to the dashboard</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const planSelect = document.getElementById('plan_id_select');
            // const planIdHiddenInput = document.getElementById('plan_id_hidden'); // Tidak diperlukan jika name select adalah plan_id
            const dynamicPriceSpan = document.getElementById('dynamic_price');
            const planPriceDisplayP = document.getElementById('plan_price_display'); // Seluruh paragraf harga

            const paymentMethodSelect = document.getElementById('payment_method');
            const paymentFeeDisplayP = document.getElementById('payment_fee_display');
            const allPlansData = @json($allAvailablePlans->keyBy('id')); // Untuk akses data plan via JS
            const paymentChannelsData = @json($paymentChannels);


            function updatePlanPriceDisplay() {
                const selectedOption = planSelect.options[planSelect.selectedIndex];
                const planId = selectedOption.value;
                const planData = allPlansData[planId];

                if (planData) {
                    dynamicPriceSpan.textContent = parseFloat(planData.price).toLocaleString('id-ID');
                    // Update durasi juga
                    let durationText = "/ Selamanya";
                    if (planData.duration_days) {
                        durationText = "/ " + planData.duration_days + " hari";
                    }
                    planPriceDisplayP.innerHTML = `Harga: Rp <span id="dynamic_price">${parseFloat(planData.price).toLocaleString('id-ID')}</span> ${durationText}`;
                }
                // Update juga fee pembayaran karena harga plan berubah
                updatePaymentFeeDisplay();
            }

            function updatePaymentFeeDisplay() {
                if (!paymentMethodSelect.value) {
                    paymentFeeDisplayP.textContent = '';
                    return;
                }

                const selectedPlanOption = planSelect.options[planSelect.selectedIndex];
                const currentPlanPrice = parseFloat(selectedPlanOption.dataset.price);

                const selectedChannelOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
                const channelInfo = JSON.parse(selectedChannelOption.dataset.channelInfo || '{}');

                if (channelInfo && channelInfo.fee_customer) {
                    const feeFlat = parseFloat(channelInfo.fee_customer.flat) || 0;
                    const feePercent = parseFloat(channelInfo.fee_customer.percent) || 0;
                    const totalFee = feeFlat + (currentPlanPrice * (feePercent / 100));
                    paymentFeeDisplayP.textContent = `Fee: Rp ${totalFee.toLocaleString('id-ID')}`;
                } else {
                    paymentFeeDisplayP.textContent = 'Fee: (Tidak diketahui)';
                }
            }


            if (planSelect) {
                planSelect.addEventListener('change', function() {
                    // planIdHiddenInput.value = this.value; // Tidak diperlukan jika name select adalah plan_id
                    updatePlanPriceDisplay();
                });
            }

            if (paymentMethodSelect) {
                paymentMethodSelect.addEventListener('change', updatePaymentFeeDisplay);
                 // Panggil sekali untuk inisialisasi fee jika ada metode pembayaran yang sudah terpilih (mis. dari old input)
                if(paymentMethodSelect.value) {
                    updatePaymentFeeDisplay();
                }
            }
        });
    </script>
</x-app-layout>