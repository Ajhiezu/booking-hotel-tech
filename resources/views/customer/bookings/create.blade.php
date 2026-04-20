@extends('layouts.app')
@section('title', "Book {$room->name}")

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
        <span>/</span>
        <a href="{{ route('hotels.show', $hotel) }}" class="hover:text-blue-600">{{ $hotel->name }}</a>
        <span>/</span>
        <span class="text-gray-900">Book Room</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        {{-- Booking Form --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h1 class="text-xl font-bold text-gray-900 mb-6">Complete Your Booking</h1>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('customer.bookings.store', $room) }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Dates --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-700 block mb-1.5">Check-in Date *</label>
                            <input type="date" name="check_in" value="{{ old('check_in', $checkIn) }}" required
                                min="{{ date('Y-m-d') }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-700 block mb-1.5">Check-out Date *</label>
                            <input type="date" name="check_out" value="{{ old('check_out', $checkOut) }}" required
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- Guest Count --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 block mb-1.5">Number of Guests *</label>
                        <select name="guests" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @for($i = 1; $i <= $room->capacity; $i++)
                                <option value="{{ $i }}" {{ old('guests', 1) == $i ? 'selected' : '' }}>{{ $i }} Guest{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Guest Info --}}
                    <div class="border-t border-gray-100 pt-5">
                        <h2 class="text-sm font-bold text-gray-900 mb-4">Guest Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1.5">Full Name *</label>
                                <input type="text" name="guest_name" value="{{ old('guest_name', auth()->user()->name) }}" required
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Email *</label>
                                    <input type="email" name="guest_email" value="{{ old('guest_email', auth()->user()->email) }}" required
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Phone *</label>
                                    <input type="tel" name="guest_phone" value="{{ old('guest_phone', auth()->user()->phone) }}" required
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="border-t border-gray-100 pt-5">
                        <h2 class="text-sm font-bold text-gray-900 mb-4">Payment Method</h2>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['bank_transfer'=>'Bank Transfer','e_wallet'=>'E-Wallet','credit_card'=>'Credit Card','cash'=>'Pay at Hotel'] as $val => $label)
                                <label class="flex items-center gap-3 border-2 rounded-xl p-3 cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 border-gray-200 transition-all">
                                    <input type="radio" name="payment_method" value="{{ $val }}" {{ old('payment_method','bank_transfer') == $val ? 'checked' : '' }} class="text-blue-600">
                                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Coupon --}}
                    <div class="border-t border-gray-100 pt-5">
                        <label class="text-sm font-semibold text-gray-700 block mb-1.5">Promo Code (Optional)</label>
                        <div class="flex gap-2">
                            <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Enter coupon code"
                                class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- Special Requests --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 block mb-1.5">Special Requests (Optional)</label>
                        <textarea name="special_requests" rows="3" placeholder="e.g. early check-in, extra pillows..."
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('special_requests') }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition-colors text-base">
                        Confirm Booking
                    </button>
                    <p class="text-center text-xs text-gray-400">You won't be charged yet. We'll confirm your booking first.</p>
                </form>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <h2 class="text-base font-bold text-gray-900 mb-4">Order Summary</h2>

                {{-- Room Info --}}
                <div class="flex gap-3 p-3 bg-gray-50 rounded-xl mb-5">
                    <div class="w-16 h-14 bg-blue-100 rounded-lg overflow-hidden flex-shrink-0">
                        @if($room->cover_image)
                            <img src="{{ $room->cover_url }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $room->name }}</p>
                        <p class="text-xs text-gray-500">{{ $hotel->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $hotel->city }}, {{ $hotel->province }}</p>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="space-y-2 text-sm mb-5">
                    <div class="flex justify-between text-gray-600">
                        <span>Check-in</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Check-out</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Duration</span>
                        <span class="font-medium">{{ $totals['nights'] }} night(s)</span>
                    </div>
                </div>

                {{-- Price Breakdown --}}
                <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Rp {{ number_format($totals['room_price'],0,',','.') }} × {{ $totals['nights'] }} nights</span>
                        <span>Rp {{ number_format($totals['subtotal'],0,',','.') }}</span>
                    </div>
                    @if($totals['discount_amount'] > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>- Rp {{ number_format($totals['discount_amount'],0,',','.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Tax (11%)</span>
                        <span>Rp {{ number_format($totals['tax_amount'],0,',','.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-900 text-base border-t border-gray-100 pt-3 mt-3">
                        <span>Total</span>
                        <span class="text-blue-600">Rp {{ number_format($totals['total_amount'],0,',','.') }}</span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-gray-100 space-y-2 text-xs text-gray-400">
                    <div class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Free cancellation within 24 hours</div>
                    <div class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Secure payment</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
