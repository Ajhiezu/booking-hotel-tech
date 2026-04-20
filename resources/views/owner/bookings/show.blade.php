@extends('layouts.owner')
@section('title', 'Booking Details')
@section('page-title', 'Booking: ' . $booking->booking_code)

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-bold text-xl text-gray-900">Guest: {{ $booking->guest_name }}</h3>
        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">{{ ucfirst($booking->status) }}</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div><p class="text-xs text-gray-500">Check-in</p><p class="font-semibold">{{ $booking->check_in->format('d M Y') }}</p></div>
        <div><p class="text-xs text-gray-500">Check-out</p><p class="font-semibold">{{ $booking->check_out->format('d M Y') }}</p></div>
        <div><p class="text-xs text-gray-500">Room</p><p class="font-semibold">{{ $booking->room?->name }}</p></div>
        <div><p class="text-xs text-gray-500">Total Paid</p><p class="font-semibold text-blue-600">Rp {{ number_format($booking->total_amount,0,',','.') }}</p></div>
    </div>

    <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-100">
        @if($booking->isPending())
            <form action="{{ route('owner.bookings.confirm', $booking) }}" method="POST" data-confirm="true" data-confirm-title="Confirm Booking?" data-confirm-text="This will notify the guest that their booking is confirmed." data-confirm-button="Yes, confirm it">
                @csrf
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">Confirm Booking</button>
            </form>
        @endif

        @if($booking->isConfirmed())
            <form action="{{ route('owner.bookings.check-in', $booking) }}" method="POST" data-confirm="true" data-confirm-title="Check-in Guest?" data-confirm-text="Mark this guest as checked in." data-confirm-button="Yes, check-in">
                @csrf
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">Check-in Guest</button>
            </form>
        @endif

        @if($booking->isCheckedIn())
            <form action="{{ route('owner.bookings.complete', $booking) }}" method="POST" data-confirm="true" data-confirm-title="Complete Stay?" data-confirm-text="Mark this stay as completed." data-confirm-button="Yes, complete it">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">Complete Stay</button>
            </form>
        @endif

        @if(!$booking->isCancelled() && !$booking->isCompleted())
            <form action="{{ route('owner.bookings.cancel', $booking) }}" method="POST" 
                  data-confirm="true" 
                  data-confirm-title="Cancel Reservation?" 
                  data-confirm-text="Are you sure you want to cancel this booking? This action is irreversible." 
                  data-confirm-type="danger"
                  data-confirm-button="Yes, cancel it">
                @csrf
                <input type="hidden" name="reason" value="Cancelled by hotel owner">
                <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 px-6 py-2.5 rounded-xl font-semibold transition-colors">Cancel Booking</button>
            </form>
        @endif
    </div>
</div>
@endsection
