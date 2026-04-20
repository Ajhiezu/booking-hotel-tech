@extends('layouts.app')
@section('title', 'Search Hotels')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Sidebar Filters --}}
        <aside class="w-full lg:w-72 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <h2 class="font-bold text-gray-900 mb-4">Filters</h2>
                <form action="{{ route('hotels.search') }}" method="GET" id="filter-form">

                    {{-- Location --}}
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Location</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="location" value="{{ request('location') }}" placeholder="City or hotel name"
                                class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Price per Night</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" min="0"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-400 text-sm">—</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" min="0"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- Star Rating --}}
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Star Rating</label>
                        <div class="flex gap-2">
                            @foreach([1,2,3,4,5] as $star)
                                <label class="cursor-pointer">
                                    <input type="radio" name="stars" value="{{ $star }}" class="sr-only" {{ request('stars') == $star ? 'checked' : '' }}>
                                    <span class="flex items-center justify-center w-10 h-10 rounded-xl border-2 text-sm font-semibold transition-all {{ request('stars') == $star ? 'bg-yellow-400 border-yellow-400 text-white' : 'border-gray-200 text-gray-500 hover:border-yellow-400' }}">
                                        {{ $star }}★
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Guest Rating --}}
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Guest Rating</label>
                        <select name="rating" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any</option>
                            <option value="4.5" {{ request('rating') == 4.5 ? 'selected' : '' }}>4.5+ Excellent</option>
                            <option value="4.0" {{ request('rating') == 4.0 ? 'selected' : '' }}>4.0+ Very Good</option>
                            <option value="3.5" {{ request('rating') == 3.5 ? 'selected' : '' }}>3.5+ Good</option>
                        </select>
                    </div>

                    {{-- Facilities --}}
                    @if($facilities->count())
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Facilities</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($facilities as $facility)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                        class="rounded text-blue-600 focus:ring-blue-500"
                                        {{ in_array($facility->id, request('facilities', [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">{{ $facility->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm">
                        Apply Filters
                    </button>
                    <a href="{{ route('hotels.search') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 mt-2">Clear all</a>
                </form>
            </div>
        </aside>

        {{-- Results --}}
        <div class="flex-1">
            {{-- Sort + Count --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <p class="text-gray-600 text-sm">
                    <span class="font-semibold text-gray-900">{{ $hotels->total() }}</span> hotels found
                    @if(request('location')) for "<span class="text-blue-600">{{ request('location') }}</span>"@endif
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Sort by:</span>
                    <select name="sort" form="filter-form" onchange="document.getElementById('filter-form').submit()"
                        class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="recommended" {{ request('sort','recommended') == 'recommended' ? 'selected' : '' }}>Recommended</option>
                        <option value="price_asc"   {{ request('sort') == 'price_asc'   ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc"  {{ request('sort') == 'price_desc'  ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="rating"      {{ request('sort') == 'rating'      ? 'selected' : '' }}>Highest Rating</option>
                        <option value="newest"      {{ request('sort') == 'newest'      ? 'selected' : '' }}>Newest</option>
                    </select>
                </div>
            </div>

            @if($hotels->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No hotels found</h3>
                    <p class="text-gray-500">Try adjusting your filters or search a different location.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    @foreach($hotels as $hotel)
                        @include('customer.partials.hotel-card', ['hotel' => $hotel])
                    @endforeach
                </div>
                {{ $hotels->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
