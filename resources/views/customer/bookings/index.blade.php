@extends('layouts.app')
@section('title', 'My Bookings')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Bookings</h1>
            <p class="text-gray-500 text-sm mt-1">Track and manage all your hotel reservations</p>
        </div>
    </div>

    {{-- Status Filters --}}
    <div class="flex gap-2 mb-6 flex-wrap">
        @php $statuses = [''=>'All', 'pending'=>'Pending', 'confirmed'=>'Confirmed', 'checked_in'=>'Checked In', 'completed'=>'Completed', 'cancelled'=>'Cancelled']; @endphp
        @foreach($statuses as $val => $label)
            <a href="{{ route('customer.bookings.index', array_merge(request()->except('page'), ['status' => $val])) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ request('status') === $val ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-blue-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @forelse($bookings as $booking)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden hover:shadow-md transition-shadow">
            <div class="flex flex-col sm:flex-row">
                {{-- Hotel Thumb --}}
                <div class="sm:w-40 h-32 sm:h-auto bg-gradient-to-br from-blue-100 to-indigo-200 flex-shrink-0">
                    @if($booking->hotel?->cover_image)
                        <img src="{{ $booking->hotel->cover_url }}" alt="" class="w-full h-full object-cover">
                    @endif
                </div>

                <div class="flex-1 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $booking->booking_code }}</span>
                                @php $bc = match($booking->status){'pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','checked_in'=>'bg-indigo-100 text-indigo-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-600'}; @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $bc }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                            </div>
                            <h2 class="text-base font-bold text-gray-900">{{ $booking->hotel?->name }}</h2>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $booking->room?->name }}</p>
                        </div>
                        <p class="text-blue-600 font-extrabold text-lg flex-shrink-0">Rp {{ number_format($booking->total_amount,0,',','.') }}</p>
                    </div>

                    <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-500">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $booking->check_in->format('d M') }} → {{ $booking->check_out->format('d M Y') }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $booking->nights }} night(s)
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-4">
                        <a href="{{ route('customer.bookings.show', $booking) }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg transition-colors font-medium">View Details</a>

                        @if($booking->isPending())
                            <form method="POST" action="{{ route('customer.bookings.cancel', $booking) }}"
                                  data-confirm="true"
                                  data-confirm-title="Cancel Booking?"
                                  data-confirm-text="Are you sure you want to cancel this booking? This action cannot be undone."
                                  data-confirm-button="Yes, cancel it"
                                  data-confirm-type="danger">
                                @csrf
                                <button type="submit" name="reason" value="Cancelled by customer" class="text-sm text-red-500 hover:text-red-700 hover:underline transition-colors font-medium">Cancel</button>
                            </form>
                        @endif

                        @if($booking->canBeReviewed())
                            <a href="{{ route('customer.bookings.show', $booking) }}#review" class="text-sm text-yellow-600 hover:underline">Write a Review</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No bookings yet</h3>
            <p class="text-gray-500 mb-6">Start exploring and book your first hotel!</p>
            <a href="{{ route('hotels.search') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors">Explore Hotels</a>
        </div>
    @endforelse

    <div class="mt-6">{{ $bookings->links() }}</div>
</div>
@endsection
