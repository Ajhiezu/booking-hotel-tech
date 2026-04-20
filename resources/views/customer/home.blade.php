@extends('layouts.app')
@section('title', 'Find Your Perfect Stay')

@section('content')
{{-- Hero Section --}}
<section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-20" style="background-image:url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1920&q=80');background-size:cover;background-position:center;"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 leading-tight">
                Find Your Perfect <span class="text-blue-300">Stay</span>
            </h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto">Discover amazing hotels across Indonesia at unbeatable prices.</p>
        </div>

        {{-- Search Box --}}
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl p-2">
            <form action="{{ route('hotels.search') }}" method="GET" class="flex flex-col md:flex-row gap-2">
                <div class="flex-1 flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <input type="text" name="location" placeholder="Where are you going?" class="bg-transparent flex-1 text-gray-900 placeholder-gray-400 focus:outline-none text-sm">
                </div>
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input type="date" name="check_in" class="bg-transparent text-gray-900 focus:outline-none text-sm" min="{{ date('Y-m-d') }}">
                </div>
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <input type="date" name="check_out" class="bg-transparent text-gray-900 focus:outline-none text-sm" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-200 hover:shadow-lg active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search
                </button>
            </form>
        </div>

        {{-- Trust badges --}}
        <div class="flex flex-wrap justify-center gap-6 mt-8 text-sm text-blue-200">
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Free cancellation
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Best price guarantee
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Verified hotels
            </div>
        </div>
    </div>
</section>

{{-- Popular Cities --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Popular Destinations</h2>
            <p class="text-gray-500 mt-1">Find hotels in top cities across Indonesia</p>
        </div>
        <a href="{{ route('hotels.search') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">See all →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($popularCities as $city)
            <a href="{{ route('hotels.search', ['location' => $city->city]) }}"
               class="group relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl overflow-hidden aspect-square flex flex-col justify-end p-4 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
                <div class="relative">
                    <p class="text-white font-bold text-sm">{{ $city->city }}</p>
                    <p class="text-blue-100 text-xs">{{ $city->hotel_count }} hotels</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- Featured Hotels --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Featured Hotels</h2>
                <p class="text-gray-500 mt-1">Handpicked top picks just for you</p>
            </div>
            <a href="{{ route('hotels.search') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">See all →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredHotels as $hotel)
                @include('customer.partials.hotel-card', ['hotel' => $hotel])
            @endforeach
        </div>
    </div>
</section>

{{-- Top Rated --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Top Rated Hotels</h2>
            <p class="text-gray-500 mt-1">Loved by our guests</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($topRatedHotels as $hotel)
            @include('customer.partials.hotel-card', ['hotel' => $hotel])
        @endforeach
    </div>
</section>

{{-- CTA Banner for Hotel Owners --}}
<section class="bg-gradient-to-r from-blue-600 to-indigo-700 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h2 class="text-3xl font-bold mb-4">Own a Hotel? List It on StayEase</h2>
        <p class="text-blue-100 mb-8 text-lg">Reach thousands of travelers and grow your business with our powerful hotel management tools.</p>
        <a href="{{ route('register') }}" class="inline-block bg-white text-blue-600 font-bold px-8 py-3 rounded-xl hover:bg-blue-50 transition-colors shadow-lg">
            Get Started Free →
        </a>
    </div>
</section>
@endsection
