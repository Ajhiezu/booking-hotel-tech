@extends('layouts.admin')
@section('title', 'Hotel Details')
@section('page-title', 'Hotel Details: ' . $hotel->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.hotels.index') }}" class="text-sm text-blue-600 hover:text-blue-700 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Hotels
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $hotel->name }}</h2>
                    <p class="text-gray-500 text-sm mt-1">Owner: <a href="{{ route('admin.users.show', $hotel->user_id) }}" class="text-blue-600 hover:underline">{{ $hotel->owner?->name }}</a></p>
                </div>
                @php
                    $sc = match($hotel->status) {
                        'pending'   => 'bg-yellow-100 text-yellow-700',
                        'approved'  => 'bg-green-100 text-green-700',
                        'rejected'  => 'bg-red-100 text-red-700',
                        'suspended' => 'bg-orange-100 text-orange-700',
                        default     => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $sc }}">
                    {{ ucfirst($hotel->status) }}
                </span>
            </div>

            @if($hotel->cover_image)
                <img src="{{ $hotel->cover_url }}" alt="" class="w-full h-64 object-cover rounded-xl mb-6">
            @endif

            <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">City / Province</p>
                    <p class="font-medium text-gray-900">{{ $hotel->city }}, {{ $hotel->province }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Star Rating</p>
                    <p class="font-medium text-gray-900">{{ $hotel->star_rating }} Star</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Contact</p>
                    <p class="text-sm text-gray-900">{{ $hotel->phone ?? '-' }}</p>
                    <p class="text-sm text-gray-900">{{ $hotel->email ?? '-' }}</p>
                </div>
            </div>

            <p class="text-sm text-gray-700 leading-relaxed">{{ $hotel->description }}</p>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Management Actions</h3>
            
            <div class="space-y-3">
                @if($hotel->status === 'pending')
                    <form method="POST" action="{{ route('admin.hotels.approve', $hotel) }}">
                        @csrf<button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-xl text-sm transition-colors">Approve Hotel</button>
                    </form>
                    <form method="POST" action="{{ route('admin.hotels.reject', $hotel) }}">
                        @csrf<button type="submit" class="w-full bg-red-100 text-red-600 hover:bg-red-200 font-semibold py-2 rounded-xl text-sm transition-colors">Reject Hotel</button>
                    </form>
                @endif

                @if($hotel->status === 'approved')
                    <form method="POST" action="{{ route('admin.hotels.suspend', $hotel) }}">
                        @csrf<button type="submit" class="w-full bg-orange-100 text-orange-700 hover:bg-orange-200 font-semibold py-2 rounded-xl text-sm transition-colors">Suspend Hotel</button>
                    </form>
                    <form method="POST" action="{{ route('admin.hotels.feature', $hotel) }}">
                        @csrf<button type="submit" class="w-full {{ $hotel->is_featured ? 'bg-gray-100 text-gray-700' : 'bg-blue-600 text-white' }} font-semibold py-2 rounded-xl text-sm transition-colors">
                            {{ $hotel->is_featured ? 'Unfeature Hotel' : 'Feature Hotel' }}
                        </button>
                    </form>
                @endif
                
                @if($hotel->status === 'suspended')
                    <form method="POST" action="{{ route('admin.hotels.approve', $hotel) }}">
                        @csrf<button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-xl text-sm transition-colors">Restore (Approve)</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.hotels.destroy', $hotel) }}" class="pt-4 border-t border-gray-100">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full text-red-600 font-semibold py-2 rounded-xl text-sm hover:bg-red-50 transition-colors">Delete Hotel Permanently</button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Hotel Stats</h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total Rooms</span>
                    <span class="font-semibold">{{ $hotel->rooms->count() }} Types</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total Bookings</span>
                    <span class="font-semibold">{{ $hotel->bookings()->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Average Rating</span>
                    <span class="font-semibold text-yellow-600">★ {{ number_format($hotel->rating_avg, 1) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
