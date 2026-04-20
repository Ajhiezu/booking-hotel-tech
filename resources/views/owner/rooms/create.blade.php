@extends('layouts.owner')
@section('title', 'Add Room')
@section('page-title', 'Add Room — ' . $hotel->name)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">{{ isset($room) ? 'Edit' : 'Add New' }} Room</h2>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-600">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ isset($room) ? route('owner.hotels.rooms.update', [$hotel, $room]) : route('owner.hotels.rooms.store', $hotel) }}"
              method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @isset($room) @method('PUT') @endisset

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Room Name *</label>
                    <input type="text" name="name" value="{{ old('name', $room->name ?? '') }}" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Room Type *</label>
                    <select name="type" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(['standard','deluxe','suite','family','villa'] as $type)
                            <option value="{{ $type }}" {{ old('type', $room->type ?? '') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-1.5">Description</label>
                <textarea name="description" rows="3" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('description', $room->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Price/Night (Rp) *</label>
                    <input type="number" name="price_per_night" value="{{ old('price_per_night', $room->price_per_night ?? '') }}" min="0" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Weekend Price (Rp)</label>
                    <input type="number" name="weekend_price" value="{{ old('weekend_price', $room->weekend_price ?? '') }}" min="0"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Total Rooms *</label>
                    <input type="number" name="total_rooms" value="{{ old('total_rooms', $room->total_rooms ?? 1) }}" min="1" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Max Guests *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $room->capacity ?? 2) }}" min="1" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Bed Type</label>
                    <select name="bed_type" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(['single','double','queen','king','twin'] as $bt)
                            <option value="{{ $bt }}" {{ old('bed_type', $room->bed_type ?? '') === $bt ? 'selected' : '' }}>{{ ucfirst($bt) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1.5">Bed Count</label>
                    <input type="number" name="bed_count" value="{{ old('bed_count', $room->bed_count ?? 1) }}" min="1"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Amenities --}}
            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-3">Room Amenities</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach(['has_wifi'=>'WiFi','has_ac'=>'Air Conditioning','has_tv'=>'TV','has_bathroom'=>'Private Bathroom','has_balcony'=>'Balcony'] as $field => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="{{ $field }}" value="1" class="rounded text-blue-600"
                                {{ old($field, ($room->{$field} ?? true) ? '1' : '') ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Cover Image --}}
            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-1.5">Room Cover Image</label>
                @isset($room)
                    @if($room->cover_image)
                        <img src="{{ $room->cover_url }}" alt="" class="w-32 h-24 object-cover rounded-lg mb-2">
                    @endif
                @endisset
                <input type="file" name="cover_image" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            {{-- Active status --}}
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="rounded text-blue-600"
                    {{ old('is_active', $room->is_active ?? true) ? 'checked' : '' }}>
                <span class="text-sm font-medium text-gray-700">Room is active and bookable</span>
            </label>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm">
                    {{ isset($room) ? 'Update Room' : 'Add Room' }}
                </button>
                <a href="{{ route('owner.hotels.rooms.index', $hotel) }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl border border-gray-200 text-sm transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
