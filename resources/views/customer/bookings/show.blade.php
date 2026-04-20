@extends('layouts.app')
@section('title', "Booking {$booking->booking_code}")

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('customer.bookings.index') }}" class="hover:text-blue-600">My Bookings</a>
        <span>/</span>
        <span class="text-gray-900 font-mono">{{ $booking->booking_code }}</span>
    </nav>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Booking Status</p>
                @php $bc = match($booking->status){'pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','checked_in'=>'bg-indigo-100 text-indigo-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-600'}; @endphp
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $bc }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 mb-1">Total Amount</p>
                <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($booking->total_amount,0,',','.') }}</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-gray-900 mb-4">Hotel Details</h3>
                <div class="flex gap-4">
                    @if($booking->hotel?->cover_image)
                        <img src="{{ $booking->hotel->cover_url }}" class="w-16 h-16 rounded-xl object-cover">
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900">{{ $booking->hotel?->name }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->room?->name }} ({{ $booking->guests }} Guests)</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $booking->hotel?->address }}</p>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 mb-4">Reservation Dates</h3>
                <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Check-in</p>
                        <p class="font-semibold text-gray-900">{{ $booking->check_in->format('d M Y') }}</p>
                    </div>
                    <div class="w-8 h-px bg-gray-300"></div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Check-out</p>
                        <p class="font-semibold text-gray-900">{{ $booking->check_out->format('d M Y') }}</p>
                    </div>
                </div>
                <p class="text-xs text-center text-gray-500 mt-2">{{ $booking->nights }} Night(s)</p>
            </div>
        </div>

        <div class="p-6 select-text">
            <h3 class="font-bold text-gray-900 mb-4">Guest Information</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div><p class="text-gray-500">Name</p><p class="font-medium mt-1">{{ $booking->guest_name }}</p></div>
                <div><p class="text-gray-500">Email</p><p class="font-medium mt-1">{{ $booking->guest_email }}</p></div>
                <div><p class="text-gray-500">Phone</p><p class="font-medium mt-1">{{ $booking->guest_phone }}</p></div>
                <div><p class="text-gray-500">Payment</p><p class="font-medium mt-1">{{ ucfirst(str_replace('_',' ',$booking->transaction?->payment_method ?? '-')) }}</p></div>
            </div>
        </div>
    </div>

    @if($booking->isPending())
        <div class="text-right">
            <form method="POST" action="{{ route('customer.bookings.cancel', $booking) }}"
                  data-confirm="true"
                  data-confirm-title="Cancel Booking?"
                  data-confirm-text="Are you sure you want to cancel this booking? This action cannot be undone."
                  data-confirm-button="Yes, cancel it"
                  data-confirm-type="danger">
                @csrf
                <button type="submit" class="text-sm bg-red-50 text-red-600 hover:bg-red-100 font-semibold px-6 py-2.5 rounded-xl transition-colors">Cancel Booking</button>
            </form>
        </div>
    @endif

    {{-- Review Section --}}
    @if($booking->review)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6 mt-6" id="review">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Your Review</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex gap-1 text-yellow-400">
                        @for($i=1; $i<=5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $booking->review->rating ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>
                </div>
                @if($booking->review->title)
                    <p class="font-bold text-gray-900 mb-1">{{ $booking->review->title }}</p>
                @endif
                <p class="text-gray-600 text-sm mb-4">{{ $booking->review->comment }}</p>
                
                @if($booking->review->owner_reply)
                    <div class="bg-gray-50 border-l-4 border-blue-500 p-4 rounded-r-xl">
                        <p class="text-xs font-semibold text-gray-900 mb-1">Hotel Response</p>
                        <p class="text-sm text-gray-600">{{ $booking->review->owner_reply }}</p>
                    </div>
                @endif
            </div>
        </div>
    @elseif($booking->canBeReviewed())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6 mt-6" id="review">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Write a Review</h3>
                <p class="text-sm text-gray-500 mt-1">Share your experience with other travelers.</p>
            </div>
            <div class="p-6">
                <form action="{{ route('customer.reviews.store', $booking) }}" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Overall Rating *</label>
                        <select name="rating" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select a rating</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Average</option>
                            <option value="2">2 - Poor</option>
                            <option value="1">1 - Terrible</option>
                        </select>
                        @error('rating') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title (Optional)</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Review Comment *</label>
                        <textarea name="comment" rows="4" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" required placeholder="Tell us about your stay...">{{ old('comment') }}</textarea>
                        @error('comment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="text-right">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mt-6 text-center">
            <svg class="w-12 h-12 text-blue-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h4 class="font-bold text-blue-900 mb-1">Reviewing not available yet</h4>
            <p class="text-sm text-blue-700">You can write a review once your booking status is marked as <span class="font-bold">Completed</span> after your stay.</p>
        </div>
    @endif
</div>
@endsection
