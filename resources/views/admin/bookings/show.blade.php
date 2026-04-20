@extends('layouts.admin')
@section('title', 'Booking Details')
@section('page-title', 'Booking: ' . $booking->booking_code)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-700 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Bookings
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4 border-b pb-2">Reservation Info</h3>
        <div class="flex justify-between py-2">
            <span class="text-gray-500">Booking Code</span>
            <span class="font-mono text-blue-600">{{ $booking->booking_code }}</span>
        </div>
        <div class="flex justify-between py-2">
            <span class="text-gray-500">Status</span>
            <span class="font-semibold">{{ ucfirst($booking->status) }}</span>
        </div>
        <div class="flex justify-between py-2">
            <span class="text-gray-500">Check-In</span>
            <span class="font-semibold">{{ $booking->check_in->format('Y-m-d') }}</span>
        </div>
        <div class="flex justify-between py-2">
            <span class="text-gray-500">Check-Out</span>
            <span class="font-semibold">{{ $booking->check_out->format('Y-m-d') }}</span>
        </div>
        <div class="flex justify-between py-2">
            <span class="text-gray-500">Nights</span>
            <span class="font-semibold">{{ $booking->nights }}</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4 border-b pb-2">Financials</h3>
        <div class="flex justify-between py-2">
            <span class="text-gray-500">Room Price ({{ $booking->nights }}x)</span>
            <span>Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between py-2">
            <span class="text-gray-500">Tax</span>
            <span>Rp {{ number_format($booking->tax_amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between py-2 border-t mt-2 pt-2">
            <span class="font-bold text-gray-900">Total Amount</span>
            <span class="font-bold text-blue-600">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
        </div>
        @if($booking->transaction)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-sm font-semibold mb-2">Transaction Status: <span class="uppercase text-green-600">{{ $booking->transaction->status }}</span></p>
            <p class="text-sm text-gray-500">Method: {{ strtoupper($booking->transaction->payment_method) }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
