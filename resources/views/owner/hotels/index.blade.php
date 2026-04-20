@extends('layouts.owner')
@section('title', 'Manage Hotels')
@section('page-title', 'My Hotels')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-bold text-gray-900">Hotel Properties</h2>
    <a href="{{ route('owner.hotels.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add New Hotel
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($hotels as $hotel)
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="h-48 bg-gray-100 relative">
            @if($hotel->cover_image)
                <img src="{{ $hotel->cover_url }}" class="w-full h-full object-cover">
            @endif
            <div class="absolute top-3 right-3">
                @php $sc = match($hotel->status) { 'approved'=>'bg-green-100 text-green-700', 'pending'=>'bg-yellow-100 text-yellow-700', 'rejected'=>'bg-red-100 text-red-700', default=>'bg-gray-100' }; @endphp
                <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $sc }}">{{ ucfirst($hotel->status) }}</span>
            </div>
        </div>
        <div class="p-5">
            <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $hotel->name }}</h3>
            <p class="text-sm text-gray-500 mb-4">{{ $hotel->city }}, {{ $hotel->province }}</p>
            
            <div class="flex gap-2">
                <a href="{{ route('owner.hotels.edit', $hotel) }}" class="flex-1 text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium py-2 rounded-xl text-sm transition-colors">Edit</a>
                <a href="{{ route('owner.hotels.rooms.index', $hotel) }}" class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-2 rounded-xl text-sm transition-colors">Rooms</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-100">
        <p class="text-gray-500 mb-4">You haven't listed any hotels yet.</p>
    </div>
    @endforelse
</div>
<div class="mt-6">{{ $hotels->links() }}</div>
@endsection
