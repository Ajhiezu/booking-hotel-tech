@extends('layouts.owner')
@section('title', isset($hotel) ? 'Edit Hotel' : 'Add Hotel')
@section('page-title', isset($hotel) ? 'Edit Hotel' : 'Register New Hotel')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm">
                <ul class="list-disc pl-4">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif
        
        <form action="{{ isset($hotel) ? route('owner.hotels.update', $hotel) : route('owner.hotels.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @isset($hotel) @method('PUT') @endisset

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Hotel Name *</label>
                    <input type="text" name="name" value="{{ old('name', $hotel->name ?? '') }}" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Star Rating *</label>
                    <select name="star_rating" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2.5">
                        @foreach([1,2,3,4,5] as $s)
                            <option value="{{ $s }}" {{ old('star_rating', $hotel->star_rating ?? 3) == $s ? 'selected' : '' }}>{{ $s }} Star</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description *</label>
                <textarea name="description" rows="4" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2.5">{{ old('description', $hotel->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold text-gray-700 mb-1">City *</label><input type="text" name="city" value="{{ old('city', $hotel->city ?? '') }}" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2.5"></div>
                <div><label class="block text-sm font-semibold text-gray-700 mb-1">Province *</label><input type="text" name="province" value="{{ old('province', $hotel->province ?? '') }}" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2.5"></div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Address *</label>
                <input type="text" name="address" value="{{ old('address', $hotel->address ?? '') }}" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2.5">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold text-gray-700 mb-1">Base Price (Rp) *</label><input type="number" name="base_price" value="{{ old('base_price', $hotel->base_price ?? '') }}" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2.5"></div>
                <div><label class="block text-sm font-semibold text-gray-700 mb-1">Cover Image</label><input type="file" name="cover_image" accept="image/*" class="w-full border border-gray-200 rounded-xl text-sm py-2 px-3"></div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Facilities</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(\App\Models\Facility::all() as $facility)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="{{ $facility->id }}" class="rounded text-blue-600"
                                {{ (is_array(old('facilities')) && in_array($facility->id, old('facilities'))) || (isset($hotel) && $hotel->facilities->contains($facility->id)) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $facility->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white font-semibold px-6 py-2.5 rounded-xl">{{ isset($hotel) ? 'Update Hotel' : 'Register Hotel' }}</button>
        </form>
    </div>
</div>
@endsection
