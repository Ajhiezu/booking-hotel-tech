@extends('layouts.owner')
@section('title', 'Manage Rooms')
@section('page-title', 'Rooms — ' . $hotel->name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500">{{ $hotel->name }}</p>
        <h1 class="text-xl font-bold text-gray-900">Room Management</h1>
    </div>
    <a href="{{ route('owner.hotels.rooms.create', $hotel) }}"
       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Room
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($rooms as $room)
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <div class="h-36 bg-gradient-to-br from-blue-50 to-indigo-100 relative">
                @if($room->cover_image)
                    <img src="{{ $room->cover_url }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                @endif
                <div class="absolute top-3 right-3">
                    <span class="text-xs {{ $room->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded-full font-medium">
                        {{ $room->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $room->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ ucfirst($room->type) }} • {{ $room->capacity }} guests</p>
                    </div>
                    <div class="text-right">
                        <p class="text-blue-600 font-bold text-sm">Rp {{ number_format($room->price_per_night,0,',','.') }}</p>
                        <p class="text-xs text-gray-400">/night</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
                    @if($room->has_wifi)  <span class="bg-gray-100 px-2 py-0.5 rounded-full">WiFi</span>  @endif
                    @if($room->has_ac)    <span class="bg-gray-100 px-2 py-0.5 rounded-full">AC</span>    @endif
                    @if($room->has_tv)    <span class="bg-gray-100 px-2 py-0.5 rounded-full">TV</span>    @endif
                    @if($room->has_balcony)<span class="bg-gray-100 px-2 py-0.5 rounded-full">Balcony</span>@endif
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('owner.hotels.rooms.edit', [$hotel, $room]) }}"
                       class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm font-medium py-1.5 rounded-lg transition-colors">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('owner.hotels.rooms.destroy', [$hotel, $room]) }}" 
                          data-confirm="true" 
                          data-confirm-title="Delete Room?" 
                          data-confirm-text="Deleting this room will also remove any future bookings associated with it. This cannot be undone." 
                          data-confirm-type="danger" 
                          data-confirm-button="Yes, delete room">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg text-sm transition-colors">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-100">
            <p class="text-gray-400 mb-4">No rooms added yet.</p>
            <a href="{{ route('owner.hotels.rooms.create', $hotel) }}" class="bg-blue-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">Add First Room</a>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $rooms->links() }}</div>
@endsection
